import * as React from "react";
import DeleteIcon from "../Components/Icons/DeleteIcon";
import Message from "../Components/Message";

const confirm = e => {
  e.stopPropagation();
  e.nativeEvent.stopImmediatePropagation();
  if (!window.confirm(`Are you sure?`)) {
    e.preventDefault();
  }
};

class ShowsList extends React.Component {
  state = {};
  regular = [];
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

    this.setState({
      regular: window.HBAContent.regular,
      events: window.HBAContent.events,
    });
  }

  render() {
    if (!this.state.regular) {
      return null;
    }

    const regularShows = this.state.regular.map(show => {
      const href = `/admin/shows/${show.legacyId}`;

      return (<tr key={show.legacyId}>
          <td><a href={href}>{show.title}</a></td>
          <td>{show.tagLine}</td>
          <td>
            <form
              method="post"
              onSubmit={confirm}
            >
              <input type="hidden"
                     name="delete-show"
                     value={show.legacyId}
              />
              <button
                className="button button--icon button--danger"
                type="submit"
                title="Delete show"
              >
                <DeleteIcon/>
              </button>
            </form>
          </td>
        </tr>
      );
    });

    const events = this.state.events.map(show => {
      const href = `/admin/shows/${show.legacyId}`;

      return (<tr key={show.legacyId}>
          <td>
            <span className={`broadcast--event-${show.type} icon--indicator`}/>
          </td>
          <td><a href={href}>{show.title}</a></td>
          <td>{show.tagLine}</td>
          <td>{show.typeTitle}</td>
          <td>
            <form
              method="post"
              onSubmit={confirm}
            >
              <input type="hidden"
                     name="delete-show"
                     value={show.legacyId}
              />
              <button
                className="button button--icon button--danger"
                type="submit"
                title="Delete show"
              >
                <DeleteIcon/>
              </button>
            </form>
          </td>
        </tr>
      );
    });

    return (
      <div>
        {this.message}
        <div className="unit">
          <h2 className="unit">New Show</h2>
          <form method="post" className="form">
            <label className="form__label-row">
              Name
              <input type="text"
                     name="new-show-name"
                     className="form__input"
              />
            </label>
            <button type="submit" className="button">Create</button>
          </form>
        </div>

        <h2 className="unit">Regular shows</h2>
        <table className="table table--striped">
          <thead>
          <tr>
            <th className="table__title-cell">Name</th>
            <th>Tag Line</th>
            <th className="table__icon-cell">
              <span className="hidden--visually">Actions</span>
            </th>
          </tr>
          </thead>
          <tbody>
          {regularShows}
          </tbody>
        </table>

        <h2 className="unit">Events</h2>
        <table className="table table--striped">
          <thead>
          <tr>
            <th />
            <th className="table__title-cell">Name</th>
            <th>Tag Line</th>
            <th>Type</th>
            <th className="table__icon-cell">
              <span className="hidden--visually">Actions</span>
            </th>
          </tr>
          </thead>
          <tbody>
          {events}
          </tbody>
        </table>
      </div>
    );
  }
}

export default ShowsList;
