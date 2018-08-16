import * as React from "react";
import DeleteIcon from "../Components/Icons/DeleteIcon";
import RightIcon from "../Components/Icons/RightIcon";

class Container extends React.Component {
  state = {
    filter: "",
    people: [],
    selectedPeople: []
  };

  componentDidMount() {
    this.setState({
      people: window.HBAContent.people || [],
      selectedPeople: window.HBAContent.selectedPeople || []
    });
  }

  removeItem(personId) {
    this.setState({
      selectedPeople: this.state.selectedPeople.filter(
        person => person.id !== personId
      )
    });
  }

  addItem(personId) {
    const newPerson = this.state.people.find(p => p.id === personId);
    let people = this.state.selectedPeople;
    people.push(newPerson);
    people = Array.from(new Set(people)); // remove duplicates

    this.setState({
      selectedPeople: people
    });
  }

  render() {
    if (!this.state.people) {
      return null;
    }

    const selectedIds = this.state.selectedPeople.map(person => person.id);

    const selectedPeople = this.state.selectedPeople.map(person => {
      return (
        <li className="selector__item" key={`selected-${person.id}`}>
          <span className="selector__item-title">{person.name}</span>
          <span className="selector__action">
            <button
              className="button button--icon button--danger"
              onClick={e => {
                e.preventDefault();
                this.removeItem(person.id);
              }}
            >
              <DeleteIcon />
            </button>
          </span>
        </li>
      );
    });

    let allPeople = this.state.people;
    if (this.state.filter.length > 0) {
      allPeople = allPeople.filter(
        p => ~p.name.toLowerCase().indexOf(this.state.filter.toLowerCase())
      );
    }
    allPeople = allPeople.map(person => {
      return (
        <li className="selector__item" key={`all-${person.id}`}>
          <span className="selector__item-title">{person.name}</span>
          <span className="selector__action">
            <button
              className="button button--icon"
              onClick={e => {
                e.preventDefault();
                this.addItem(person.id);
              }}
            >
              <RightIcon />
            </button>
          </span>
        </li>
      );
    });

    return (
      <React.Fragment>
        <input type="hidden" value={selectedIds.join(",")} name="people" />
        <h2 className="unit">Attach people to show</h2>
        <div className="selector">
          <div className="selector__from">
            <h3 className="selector__title e unit">Choose people</h3>
            <div className="unit">
              <input
                className="form__input"
                type="text"
                value={this.state.filter}
                onChange={ev => {
                  this.setState({
                    filter: ev.target.value
                  });
                }}
                placeholder="Filter..."
              />
            </div>
            <ul className="selector__list selector__list--limited">
              {allPeople}
            </ul>
          </div>
          <div className="selector__arrow">
            <RightIcon />
          </div>
          <div className="selector__to">
            <h3 className="selector__title e unit">Currently attached</h3>
            <ul className="selector__list selector__list--limited">
              {selectedPeople}
            </ul>
          </div>
        </div>
      </React.Fragment>
    );
  }
}

export default Container;
