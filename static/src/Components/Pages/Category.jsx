import * as React from "react";

export default (props) => {
  let moveUpButton = null;
  let moveDownButton = null;
  if (props.onMoveUp) {
    moveUpButton = (
      <button className="button button--icon" title="Move Category Up" onClick={props.onMoveUp}>
        <svg viewBox="0 0 24 24">
          <path fill="#000000" d="M14,20H10V11L6.5,14.5L4.08,12.08L12,4.16L19.92,12.08L17.5,14.5L14,11V20Z"/>
        </svg>
      </button>
    );
  }
  if (props.onMoveDown) {
    moveDownButton = (
      <button className="button button--icon" title="Move Category Down" onClick={props.onMoveDown}>
        <svg viewBox="0 0 24 24">
          <path fill="#000000" d="M10,4H14V13L17.5,9.5L19.92,11.92L12,19.84L4.08,11.92L6.5,9.5L10,13V4Z"/>
        </svg>
      </button>
    );
  }

  return (
    <tr className="pages--category">
      <td>
        <form method="post">
          <input type="hidden"
                 name="update-category"
                 value={props.category.id}/>
          <input type="text"
                 name="category-title"
                 defaultValue={props.category.title}/>
          <button className="button button--icon"
                  type="submit"
                  title="Edit category title">
            <svg viewBox="0 0 24 24">
              <path fill="#000000" d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
            </svg>
          </button>
        </form>
      </td>
      <td>{moveUpButton}</td>
      <td>{moveDownButton}</td>
      <td>
        <form method="post" onSubmit={(e) => {
          if (!window.confirm(`
            All pages in this category will be moved to "Uncategorised."
            Are you sure?
          `)) {
            e.preventDefault();
          }
        }}>
          <input type="hidden"
                 name="delete-category"
                 value={props.category.id}/>
          <button className="button button--icon"
                  type="submit"
                  title="Delete category">
            <svg viewBox="0 0 24 24">
              <path fill="#000000"
                    d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
            </svg>
          </button>
        </form>
      </td>
    </tr>
  );
}
