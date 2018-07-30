import * as React from "react";
import LeftIcon from "../Components/Icons/LeftIcon";

class ShowPicker extends React.Component {
  state = {
    filter: "",
    newShowTitle: "",
    makingShow: false,
    showType: 0,
    shows: []
  };

  componentDidMount() {
    this.setState({
      shows: this.props.shows || []
    });
  }

  makeNewShow() {
    this.setState({
      makingShow: true
    });

    // post new show
    fetch("/admin/shows", {
      method: "POST",
      credentials: "include",
      body: JSON.stringify({
        showName: this.state.newShowTitle,
        showType: this.state.showType,
        includeAll: false // todo - support true with a prop (allowTypes)
      }),
      headers: {
        "content-type": "application/json"
      }
    })
      .then(res => res.json())
      .then(data => {
        this.props.onUpdateShows(data);
        this.setState({
          filter: this.state.newShowTitle,
          shows: data,
          newShowTitle: "",
          makingShow: false
        });
      });
  }

  render() {
    if (!this.state.shows) {
      return null;
    }

    let allShows = this.state.shows;
    if (this.state.filter.length > 0) {
      allShows = allShows.filter(
        p => ~p.title.toLowerCase().indexOf(this.state.filter.toLowerCase())
      );
    }
    allShows = allShows.map(programme => {
      return (
        <li className="selector__item" key={`all-${programme.legacyId}`}>
          <span className="selector__action">
            <button
              className="button button--icon"
              onClick={e => {
                e.preventDefault();
                this.props.onSelect(programme.legacyId);
              }}
            >
              <LeftIcon />
            </button>
          </span>
          <span className="selector__item-title selector__item-title--label">
            {programme.title}
          </span>
        </li>
      );
    });

    let showType = null;
    if (this.props.allowShowType) {
      showType = "TYPE"; // todo
    }

    return (
      <React.Fragment>
        <h3 className="selector__title e unit">Quick-make new show</h3>
        <div className="unit">
          <form onSubmit={e => {}}>
            <label className="form__label-row">
              Show title
              <input
                className="form__input"
                type="text"
                value={this.state.newShowTitle}
                onChange={e => {
                  this.setState({
                    newShowTitle: e.target.value
                  });
                }}
                required
              />
            </label>
            {showType}
            <p className="text--right">
              <button
                className="button"
                disabled={
                  this.state.makingShow || this.state.newShowTitle === ""
                }
                onClick={e => {
                  e.preventDefault();
                  this.makeNewShow();
                }}
              >
                {this.state.makingShow ? "Creating..." : "Create show"}
              </button>
            </p>
          </form>
        </div>

        <h3 className="selector__title e unit">Choose shows</h3>
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
        <ul className="selector__list selector__list--limited">{allShows}</ul>
      </React.Fragment>
    );
  }
}

export default ShowPicker;
