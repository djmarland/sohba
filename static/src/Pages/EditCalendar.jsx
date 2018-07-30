import * as React from "react";
import isAfter from "date-fns/is_after";
import isEqual from "date-fns/is_equal";
import startOfMonth from "date-fns/start_of_month";
import subMonths from "date-fns/sub_months";

const monthDisplayOptions = {
  month: "long",
  year: "numeric"
};

class EditCalendar extends React.Component {
  state = {};

  componentDidMount() {
    this.setState({
      earliestDate: startOfMonth(new Date(window.HBAContent.earliestDate)),
      latestDate: startOfMonth(new Date(window.HBAContent.latestDate))
    });
  }

  getYearsList() {
    let counter = this.state.latestDate;
    let currentYear = null;
    let currentYearData = {
      year: null,
      months: []
    };
    const years = [];

    while (
      isAfter(counter, this.state.earliestDate)
      || isEqual(counter, this.state.earliestDate)
      ) {

      const year = counter.getFullYear();
      if (currentYear !== null && currentYear !== year) {
        years.push({...currentYearData});
        currentYearData = {
          year: currentYear,
          months: []
        };
      }

      currentYear = year;
      currentYearData.year = year;
      currentYearData.months.push(counter);

      counter = subMonths(counter, 1);
    }

    years.push(currentYearData);

    return years;
  }

  makeMonths(monthList, showGenerate) {
    const list = monthList.map(month => (
      <li key={`${month.getFullYear()}${month.getMonth()}`}>
        <div className="calendar">
          <table className="calendar__table">
            <caption>{month.toLocaleString("en-GB", monthDisplayOptions)}</caption>
            <thead>
            <tr>
              <th><abbr title="Monday">M</abbr></th>
              <th><abbr title="Tuesday">T</abbr></th>
              <th><abbr title="Wednesday">W</abbr></th>
              <th><abbr title="Thursday">T</abbr></th>
              <th><abbr title="Friday">F</abbr></th>
              <th><abbr title="Saturday">S</abbr></th>
              <th><abbr title="Sunday">S</abbr></th>
            </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </li>
    ));
    if (showGenerate) {
      list.unshift(
        <li key="genrate">
          GENERATE!!
        </li>
      );
    }

    return list;
  }

  render() {
    if (!this.state.earliestDate) {
      return null;
    }

    const years = this.getYearsList();

    let showMakeNew = 0;
    const content = years.map(year => {
      showMakeNew++;
      return (
        <React.Fragment>
          <h2>{year.year}</h2>
          <ul>
            {this.makeMonths(year.months, showMakeNew === 1)}
          </ul>
        </React.Fragment>
      );
    });

    return (
      <div>
        {content}
      </div>
    );
  }
}

export default EditCalendar;
