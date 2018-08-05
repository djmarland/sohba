import * as React from "react";
import Message from "../Components/Message";
import LeftIcon from "../Components/Icons/LeftIcon";
import ShowPicker from "../Container/ShowPicker";
import DeleteIcon from "../Components/Icons/DeleteIcon";

const compareTime = (broadcast1, broadcast2) => {
  const str1 = broadcast1.time;
  const str2 = broadcast2.time;

  if (str1 === str2) {
    return 0;
  }

  const time1 = str1.split(":");
  const time2 = str2.split(":");

  for (let i = 0; i < time1.length; i++) {
    if (time1[i] > time2[i]) {
      return 1;
    } else if (time1[i] < time2[i]) {
      return -1;
    }
  }
};

class EditDate extends React.Component {
  state = {};
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

    this.types = window.HBAContent.types;

    this.setState({
      listings: window.HBAContent.listings,
      allShows: window.HBAContent.allProgrammes
    });
  }

  removeItem(broadcastId) {
    this.setState({
      listings: this.state.listings.filter(
        broadcast => broadcast.id !== broadcastId
      )
    });
  }

  addItem(showId) {
    const newShow = this.state.allShows.find(s => s.legacyId === showId);
    let shows = this.state.listings;
    shows.push({
      id: Date.now(), // temporary in-page ID
      time: "00:00", // place it at midnight
      programme: newShow
    });

    this.setState({
      listings: shows
    });
  }

  updateTime(broadcastId, newTime) {
    const listings = this.state.listings.map(listing => {
      if (listing.id === broadcastId) {
        listing.time = newTime;
      }
      return listing;
    });
    this.setState({ listings });
  }

  updateInternalNote(broadcastId, newNote) {
    const listings = this.state.listings.map(listing => {
      if (listing.id === broadcastId) {
        listing.internalNote = newNote;
      }
      return listing;
    });
    this.setState({ listings });
  }

  updatePublicNote(broadcastId, newNote) {
    const listings = this.state.listings.map(listing => {
      if (listing.id === broadcastId) {
        listing.publicNote = newNote;
      }
      return listing;
    });
    this.setState({ listings });
  }

  render() {
    if (!this.state.listings) {
      return null;
    }

    let listings = this.state.listings;
    listings.sort(compareTime);

    listings = listings.map(broadcast => {
      return (
        <li key={broadcast.id} className={`small-unit broadcast--event-${broadcast.programme.type}`}>
          <div className="selector__item selector__item--plain">
            <input
              type="time"
              className="form__input form__input--inline form__input--compact"
              value={broadcast.time}
              required
              onChange={e => {
                this.updateTime(broadcast.id, e.target.value);
              }}
            />
            <span className="selector__item-title selector__item-title--label">
              {broadcast.programme.title} <a href={`/admin/shows/${broadcast.programme.legacyId}`}
                                             target="_blank">⇗</a>
            </span>
            <span className="selector__action">
              <button
                className="button button--danger button--icon"
                onClick={e => {
                  this.removeItem(broadcast.id);
                }}
              >
                <DeleteIcon/>
              </button>
            </span>
          </div>
          <label className="form__note-row">
            <abbr
              className="form__note-label"
              title="A note that will only be displayed here and on the studio printout">
              Internal note:
            </abbr>
            <input
              type="text"
              className="form__input form__input--compact"
              value={broadcast.internalNote || ""}
              onChange={e => {
                this.updateInternalNote(broadcast.id, e.target.value);
              }}
            />
          </label>
          <label className="form__note-row">
            <abbr
              className="form__note-label"
              title="A note that will be displayed on the website for the public">
              Public note:
            </abbr>
            <input
              type="text"
              className="form__input form__input--compact"
              value={broadcast.publicNote || ""}
              onChange={e => {
                this.updatePublicNote(broadcast.id, e.target.value);
              }}
            />
          </label>
        </li>
      );
    });

    const listingData = this.state.listings.map(broadcast => {
      return {
        time: broadcast.time,
        programmeId: broadcast.programme.id,
        programmeLegacyId: broadcast.programme.legacyId,
        internalNote: broadcast.internalNote,
        publicNote: broadcast.publicNote
      };
    });

    return (
      <React.Fragment>
        {this.message}
        <div className="unit">
          <div className="selector">
            <div className="selector__main">
              <ol className="selector__list">{listings}</ol>
            </div>
            <div className="selector__arrow">
              <LeftIcon/>
            </div>
            <div className="selector__source">
              <ShowPicker
                shows={this.state.allShows}
                onSelect={showId => {
                  this.addItem(showId);
                }}
                onUpdateShows={allShows => {
                  this.setState({ allShows });
                }}
                types={this.types}
                allowShowType
              />
            </div>
          </div>
        </div>

        <form method="POST">
          <input
            type="hidden"
            name="listings"
            value={JSON.stringify(listingData)}
          />
          <div className="text--right">
            <button type="submit" className="button">
              Save
            </button>
          </div>
        </form>
      </React.Fragment>
    );
  }
}

export default EditDate;
