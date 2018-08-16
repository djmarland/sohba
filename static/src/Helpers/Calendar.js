import isBefore from "date-fns/is_before";
import isAfter from "date-fns/is_after";
import isEqual from "date-fns/is_equal";
import isSameWeek from "date-fns/is_same_week";
import isSameMonth from "date-fns/is_same_month";

import startOfWeek from "date-fns/start_of_week";
import startOfMonth from "date-fns/start_of_month";
import startOfYear from "date-fns/start_of_year";

import endOfWeek from "date-fns/end_of_week";
import endOfMonth from "date-fns/end_of_month";
import endOfYear from "date-fns/end_of_year";

import addDays from "date-fns/add_days";
import addMonths from "date-fns/add_months";
import addYears from "date-fns/add_years";

const weekOpts = { weekStartsOn: 1 };

const makeWeeks = month => {
  const firstOfMonthWeek = startOfWeek(startOfMonth(month), weekOpts);
  const endOfMonthWeek = endOfWeek(endOfMonth(month), weekOpts);

  // set the counter to the monday of the week
  let counter = firstOfMonthWeek;
  let weeks = [];
  let currentWeek = null;
  let currentWeekData = [];

  while (
    isBefore(counter, endOfMonthWeek) ||
    isEqual(counter, endOfMonthWeek)
  ) {
    if (!currentWeek || !isSameWeek(currentWeek, counter, weekOpts)) {
      if (currentWeek) {
        weeks.push(currentWeekData);
      }
      currentWeekData = [];
      currentWeek = counter;
    }

    currentWeekData.push(isSameMonth(counter, month) ? counter : null);
    counter = addDays(counter, 1);
  }
  weeks.push(currentWeekData);
  return weeks;
};

const makeMonths = (year, fromLimit, toLimit) => {
  const fromMonth = startOfYear(year);
  const toMonth = endOfYear(year);

  const from = isBefore(fromLimit, fromMonth) ? fromMonth : fromLimit;
  const to = isAfter(toLimit, toMonth) ? toMonth : toLimit;

  const months = [];
  let counter = from;

  while (isBefore(counter, to) || isEqual(counter, to)) {
    months.push(makeWeeks(counter));
    counter = addMonths(counter, 1);
  }

  return months;
};

/**
 * Returns a nested array of years -> months -> weeks -> days
 * @param from Date
 * @param to Date
 */
export const makeCalendar = (from, to) => {
  const fromYear = startOfYear(from);

  const years = [];
  let counter = fromYear;

  while (isBefore(counter, to) || isEqual(counter, to)) {
    years.push(makeMonths(counter, from, to));
    counter = addYears(counter, 1);
  }
  return years;
};

export const findDayInMonth = month => {
  let dayOfMonth = null;
  let count = 0;
  while (dayOfMonth === null) {
    dayOfMonth = month[0][count++];
  }
  return dayOfMonth;
};

export const findDayInLastMonthOfYear = year => {
  return findDayInMonth(year[year.length - 1]);
};
