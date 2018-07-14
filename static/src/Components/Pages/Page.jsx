import * as React from "react";
import UpIcon from "../Icons/UpIcon";
import DownIcon from "../Icons/DownIcon";
import DeleteIcon from "../Icons/DeleteIcon";

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

  return (
    <tr className="pages--page">
      <td>
        <a href={`/admin/pages/${props.page.id}`}>{props.page.title}</a>
      </td>
      <td className="pages__button-field">{moveUpButton}</td>
      <td className="pages__button-field">{moveDownButton}</td>
      <td className="pages__button-field">
        <form
          method="post"
          onSubmit={e => {
            if (!window.confirm(`Are you sure?`)) {
              e.preventDefault();
            }
          }}
        >
          <input type="hidden" name="delete-page" value={props.page.id} />
          <button
            className="button button--icon button--danger"
            type="submit"
            title="Delete page"
          >
            <DeleteIcon />
          </button>
        </form>
      </td>
    </tr>
  );
};
