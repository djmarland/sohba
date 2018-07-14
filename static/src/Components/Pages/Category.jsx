import * as React from "react";
import UpIcon from "../Icons/UpIcon";
import DownIcon from "../Icons/DownIcon";
import DeleteIcon from "../Icons/DeleteIcon";
import TickIcon from "../Icons/TickIcon";
import Page from "./Page";

export default props => {
  let moveUpButton = null;
  let moveDownButton = null;
  if (props.onMoveUp) {
    moveUpButton = (
      <button
        className="button button--icon"
        title="Move Category Up"
        onClick={props.onMoveUp}
      >
        <UpIcon />
      </button>
    );
  }
  if (props.onMoveDown) {
    moveDownButton = (
      <button
        className="button button--icon"
        title="Move Category Down"
        onClick={props.onMoveDown}
      >
        <DownIcon />
      </button>
    );
  }

  const pages = props.category.pagesInCategory.map(page => (
    <Page key={page.id} page={page} />
  ));

  return (
    <React.Fragment>
      <tr className="pages--category">
        <td>
          <form method="post" className="form form--category">
            <input
              type="hidden"
              name="update-category"
              value={props.category.id}
            />
            <input
              type="text"
              name="category-title"
              defaultValue={props.category.title}
            />
            <button
              className="button button--icon"
              type="submit"
              title="Edit category title"
            >
              <TickIcon />
            </button>
          </form>
        </td>
        <td className="pages__button-field">{moveUpButton}</td>
        <td className="pages__button-field">{moveDownButton}</td>
        <td className="pages__button-field pages__button-field--delete">
          <form
            method="post"
            onSubmit={e => {
              if (
                !window.confirm(`
            All pages in this category will be moved to "Uncategorised."
            Are you sure?
          `)
              ) {
                e.preventDefault();
              }
            }}
          >
            <input
              type="hidden"
              name="delete-category"
              value={props.category.id}
            />
            <button
              className="button button--icon button--danger"
              type="submit"
              title="Delete category"
            >
              <DeleteIcon />
            </button>
          </form>
        </td>
      </tr>
      {pages}
    </React.Fragment>
  );
};
