import * as React from "react";
import DeleteIcon from "../Components/Icons/DeleteIcon";
import TickIcon from "../Components/Icons/TickIcon";
import Message from "../Components/Message";

class Container extends React.Component {
  state = {};
  people = [];
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
      people: window.HBAContent.people
    });
  }

  render() {
    if (!this.state.people) {
      return null;
    }

    const people = this.state.people.map(person => {
      const isOnCommittee = person.isOnCommittee ? (
        <div className="icon icon--indicator">
          <TickIcon/>
        </div>
      ) : null;
      const committeeTitle = person.isOnCommittee ?
        person.committeeTitle : null;

      const committeeOrder = person.isOnCommittee ?
        person.committeeOrder : null;


      return (<tr key={person.legacyId}>
          <td><a href={`/admin/people/${person.legacyId}`}>{person.name}</a></td>
          <td>{isOnCommittee}</td>
          <td>{committeeTitle}</td>
          <td>{committeeOrder}</td>
          <td>
            <form
              method="post"
              onSubmit={e => {
                if (!window.confirm(`Are you sure?`)) {
                  e.preventDefault();
                }
              }}
            >
              <input type="hidden"
                     name="delete-person"
                     value={person.legacyId}
              />
              <button
                className="button button--icon button--danger"
                type="submit"
                title="Delete person"
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
          <h2 className="unit">New Person</h2>
          <form method="post" className="form">
            <label className="form__label-row">
              Name
              <input type="text"
                     name="new-person-name"
                     className="form__input"
              />
            </label>
            <button type="submit" className="button">Create</button>
          </form>
        </div>

        <table className="table table--striped">
          <thead>
          <tr>
            <th>Name</th>
            <th className="table__icon-cell">Exec?</th>
            <th>Exec Title</th>
            <th>Exec order</th>
            <th className="table__icon-cell"><span className="hidden--visually">Actions</span></th>
          </tr>
          </thead>
          <tbody>
          {people}
          </tbody>
        </table>
      </div>
    );
  }
}

export default Container;
