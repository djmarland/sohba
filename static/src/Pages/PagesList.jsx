import * as React from "react";

import PageCategory from "../Components/Pages/Category";
import Page from "../Components/Pages/Page";

const arrayMove = (arr, oldIndex, newIndex) => {
  if (newIndex >= arr.length) {
    let k = newIndex - arr.length + 1;
    while (k--) {
      arr.push(undefined);
    }
  }
  arr.splice(newIndex, 0, arr.splice(oldIndex, 1)[0]);
  return arr; // for testing
};

class Container extends React.Component {
  state = {};

  componentDidMount() {
    this.setState({
      categories: window.HBAContent.categories,
      uncategorised: window.HBAContent.uncategorised
    });
  }

  onCategoryMove(categoryIndex, moveBy) {
    let categories = this.state.categories;
    arrayMove(categories, categoryIndex, categoryIndex + moveBy);
    this.setState({
      categories
    });

    let categoryNumbers = 1;
    let categoryPositions = {};

    categories.forEach(category => {
      categoryPositions[category.id] = categoryNumbers;
      categoryNumbers++;
    });

    fetch("/admin/pages", {
      method: "POST",
      credentials: "include",
      body: JSON.stringify(categoryPositions),
      headers: {
        "content-type": "application/json"
      }
    });
  }

  render() {
    if (!this.state.categories) {
      return null;
    }

    const numCategories = this.state.categories.length;
    const categories = this.state.categories.map((cat, i) => {
      return (
        <PageCategory
          key={cat.id}
          category={cat}
          onMoveUp={
            i !== 0
              ? () => {
                  this.onCategoryMove(i, -1);
                }
              : null
          }
          onMoveDown={
            i !== numCategories - 1
              ? () => {
                  this.onCategoryMove(i, 1);
                }
              : null
          }
          onCategoryDelete={() => {
            this.onCategoryDelete(cat.id);
          }}
        />
      );
    });

    const uncategorised = this.state.uncategorised.map(page => (
      <Page key={page.id} page={page} />
    ));

    return (
      <div className="t-page-list">
        <div className="t-page-list__new-page">
          <h2 className="unit">New Page</h2>
          <form method="post" className="form">
            <label className="form__label-row">
              Page Title
              <input
                type="text"
                name="new-page-title"
                className="form__input"
              />
            </label>
            <button type="submit" className="button">
              Create
            </button>
          </form>
        </div>
        <div className="t-page-list__new-category">
          <h2 className="unit">New Category</h2>
          <form method="post" className="form">
            <label className="form__label-row">
              Category Title
              <input
                type="text"
                name="new-category-title"
                className="form__input"
              />
            </label>
            <button type="submit" className="button">
              Create
            </button>
          </form>
        </div>

        <div className="t-page-list__pages">
          <h2 className="unit">Categories & Pages</h2>
          <table className="table">
            <thead className="hidden--visually">
              <tr>
                <td>Category or page name</td>
                <td>Move up</td>
                <td>Move down</td>
                <td>Delete</td>
              </tr>
            </thead>
            <tbody>{categories}</tbody>
          </table>
          <h2>Uncategorised pages</h2>
          <table className="table">
            <thead className="hidden--visually">
              <tr>
                <td>Page name</td>
                <td>Delete</td>
              </tr>
            </thead>
            <tbody>{uncategorised}</tbody>
          </table>
        </div>
      </div>
    );
  }
}

export default Container;
