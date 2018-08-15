import * as React from "react";
import Message from "../Components/Message";
import ImagesList from "../Container/ImagesList";
import Modal from "../Container/Modal";

class PersonDetail extends React.Component {
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

    this.setState({
      person: window.HBAContent.person,
      showExec: !!window.HBAContent.person.isOnCommittee,
      image: window.HBAContent.person.image || null
    });
  }

  addImage(image) {
    this.setState({
      image: {
        src: image.src,
        id: image.id
      }
    });
    this.refs.pickerModal.close();
  }

  render() {
    if (!this.state.person) {
      return null;
    }

    let execDetail = null;
    if (this.state.showExec) {
      execDetail = (
        <React.Fragment>
          <label className="form__label-row">
            Committee Title
            <input
              type="text"
              name="exec-title"
              className="form__input"
              defaultValue={this.state.person.committeeTitle}
              required
            />
          </label>
          <label className="form__label-row">
            Committee Order
            <select
              className="form__input"
              name="exec-position"
              required
              defaultValue={this.state.person.committeeOrder}
            >
              {[...Array(10).keys()].map(k => (
                <option value={k + 1} key={`order-${k + 1}`}>
                  {k + 1}
                </option>
              ))}
            </select>
          </label>
        </React.Fragment>
      );
    }

    let image = <p>No image</p>;
    let detach = null;
    if (this.state.image) {
      image = <img src={this.state.image.src} />;
      detach = (
        <button
          className="button button--danger"
          onClick={ev => {
            ev.preventDefault();
            this.setState({
              image: null
            });
          }}
        >
          Detach
        </button>
      );
    }

    const imageModal = (
      <Modal ref="pickerModal">
        <ImagesList onSelect={this.addImage.bind(this)} />
      </Modal>
    );

    return (
      <React.Fragment>
        {this.message}
        <form method="post" className="form">
          <div className="t-person-edit">
            <div className="t-person-edit__detail">
              <h2 className="unit">Basic details</h2>

              <label className="form__label-row">
                Name
                <input
                  type="text"
                  name="name"
                  className="form__input"
                  defaultValue={this.state.person.name}
                  required
                />
              </label>

              <label className="form__label-row">
                <input
                  type="checkbox"
                  className="form__input"
                  checked={this.state.showExec}
                  name="on-exec"
                  value="1"
                  onChange={() => {
                    this.setState({
                      showExec: !this.state.showExec
                    });
                  }}
                />{" "}
                On Exec Committee?
              </label>
              {execDetail}
            </div>
            <div className="t-person-edit__image">
              <h2 className="unit">Associated Image</h2>

              <input
                type="hidden"
                name="image-id"
                value={this.state.image ? this.state.image.id : ''}
              />
              <div className="t-person-edit__image-box">{image}</div>
              <div className="t-person-edit__image-actions">
                <button
                  className="button"
                  onClick={ev => {
                    ev.preventDefault();
                    this.refs.pickerModal.open();
                  }}
                >
                  Choose
                </button>
                {detach}
              </div>
            </div>
            <div className="t-person-edit__submit">
              <button className="button" type="submit">
                Save
              </button>
            </div>
          </div>
        </form>
        {imageModal}
      </React.Fragment>
    );
  }
}

export default PersonDetail;
