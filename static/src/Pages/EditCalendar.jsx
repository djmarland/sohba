import * as React from "react";
import startOfMonth from "date-fns/start_of_month";
import addMonths from "date-fns/add_months";
import getMonth from "date-fns/get_month";
import getYear from "date-fns/get_year";
import dateFormat from "date-fns/format";
import {
  findDayInLastMonthOfYear,
  findDayInMonth,
  makeCalendar
} from "../Helpers/Calendar";
import PrintIcon from "../Components/Icons/PrintIcon";
import DeleteIcon from "../Components/Icons/DeleteIcon";
import Message from "../Components/Message";

const monthDisplayOptions = {
  month: "long",
  year: "numeric"
};

class EditCalendar extends React.Component {
  state = {};
  message = null;

  componentDidMount() {
    if (window.HBAContent.message) {
      this.message = (
        <Message
          message={window.HBAContent.message.message}
          type={window.HBAContent.message.type}
        />
      );
    }
    this.highlightDates = window.HBAContent.highlightDates || [];

    this.setState({
      earliestDate: startOfMonth(new Date(window.HBAContent.earliestDate)),
      latestDate: startOfMonth(new Date(window.HBAContent.latestDate))
    });
  }

  makeDays(days, weekKey) {
    return days.map((day, i) => {
      const dayKey = `${dayKey}-w${i}`;

      let content = null;
      if (day) {
        const dateFormatted = dateFormat(day, "YYYY-MM-DD");
        content = (
          <a
            href={`/admin/calendar/${dateFormatted}`}
            className={
              this.highlightDates.find(d => d === dateFormatted)
                ? "calendar__day--highlight"
                : ""
            }
          >
            {day.getDate()}
          </a>
        );
      }
      return <td key={dayKey}>{content}</td>;
    });
  }

  makeWeeks(weeks, monthKey) {
    return weeks.map((week, i) => {
      const weekKey = `${monthKey}-w${i}`;

      return <tr key={weekKey}>{this.makeDays(week, weekKey)}</tr>;
    });
  }

  makeMonths(monthList, showGenerate) {
    const list = monthList.map(month => {
      const monthDate = findDayInMonth(month);
      const monthkey = dateFormat(monthDate, "YYYY-MM");

      return (
        <li key={monthkey} className="t-calendar__month">
          <div className="calendar">
            <table className="calendar__table">
              <caption>
                {monthDate.toLocaleString("en-GB", monthDisplayOptions)}
                <span className="calendar__action">
                  <a
                    href={`/admin/calendar/${monthkey}`}
                    title="Print this month"
                  >
                    <PrintIcon />
                  </a>
                </span>
                <span className="calendar__action calendar__action--after">
                  <form
                    method="post"
                    onSubmit={e => {
                      if (
                        !window.confirm(
                          `Are you sure? The data cannot be recovered`
                        )
                      ) {
                        e.preventDefault();
                      }
                    }}
                  >
                    <input type="hidden" name="delete-month" value={monthkey} />
                    <button
                      className="button-reset link--danger"
                      title="Delete this month"
                    >
                      <DeleteIcon />
                    </button>
                  </form>
                </span>
              </caption>
              <thead>
                <tr>
                  <th>
                    <abbr title="Monday">M</abbr>
                  </th>
                  <th>
                    <abbr title="Tuesday">T</abbr>
                  </th>
                  <th>
                    <abbr title="Wednesday">W</abbr>
                  </th>
                  <th>
                    <abbr title="Thursday">T</abbr>
                  </th>
                  <th>
                    <abbr title="Friday">F</abbr>
                  </th>
                  <th>
                    <abbr title="Saturday">S</abbr>
                  </th>
                  <th>
                    <abbr title="Sunday">S</abbr>
                  </th>
                </tr>
              </thead>
              <tbody>{this.makeWeeks(month, monthkey)}</tbody>
            </table>
          </div>
        </li>
      );
    });
    if (showGenerate) {
      list.unshift(
        <li
          key="generate"
          className="t-calendar__month t-calendar__month--generate"
        >
          GENERATE!!
        </li>
      );
    }

    return list;
  }

  getPossibleFirstYear(years) {
    const dayOfLastMonth = findDayInLastMonthOfYear(years[0]);

    if (getMonth(dayOfLastMonth) === 11) {
      // december, so being making new year
      const nextMonth = addMonths(dayOfLastMonth, 1);
      return this.makeYear(nextMonth, [], nextMonth);
    }
    return null;
  }

  makeYear(date, months, generateDate) {
    const year = getYear(date);

    let generate = null;
    if (generateDate) {
      const date = generateDate.toLocaleString("en-GB", monthDisplayOptions);

      generate = (
        <li
          key={`generate-${date}`}
          className="t-calendar__month t-calendar__month--generate"
        >
          <button
            className="unit button button--box"
            onClick={e => {
              this.setState({
                latestDate: generateDate
              });
            }}
          >
            Generate<br />
            {date}
          </button>
        </li>
      );
    }

    return (
      <div className="t-calendar__year" key={`year-${year}`}>
        <h2 className="t-calendar__year-title">{year}</h2>
        <ul className="t-calendar__months">
          {generate}
          {this.makeMonths(months.reverse())}
        </ul>
      </div>
    );
  }

  render() {
    if (!this.state.earliestDate) {
      return null;
    }

    // get the full calendar (in reverse year order)
    const years = makeCalendar(
      this.state.earliestDate,
      this.state.latestDate
    ).reverse();
    const firstYear = this.getPossibleFirstYear(years);

    let showMakeNew = firstYear ? 0 : 1;
    const content = years.map(year => {
      const yearDate = findDayInLastMonthOfYear(year);

      let generateDate = null;
      if (showMakeNew-- > 0) {
        generateDate = addMonths(yearDate, 1);
      }

      return this.makeYear(yearDate, year, generateDate);
    });

    if (firstYear) {
      content.unshift(firstYear);
    }

    return (
      <React.Fragment>
        {this.message}
        <div className="t-calendar">{content}</div>
      </React.Fragment>
    );
  }
}

export default EditCalendar;
