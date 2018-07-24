import * as React from "react";
import Message from "../Components/Message";
import ImagesList from "../Container/ImagesList";
import Modal from "../Container/Modal";
import RichTextEditor from "../Container/RichTextEditor";

class ShowDetail extends React.Component {
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
      show: window.HBAContent.show,
      types: window.HBAContent.types,
      image: window.HBAContent.show.image || null
    });
  }

  addImage(image) {
    this.setState({
      image : {
        src: image.src,
        id: image.id
      }
    });
    this.refs.pickerModal.close();
  }

  render() {

    if (!this.state.show) {
      return null;
    }

    let image = <p>No image</p>;
    let detach = null;
    if (this.state.image) {
      image = <img src={this.state.image.src}/>;
      detach = (
        <button className="button button--danger"
                onClick={(ev) => {
                  ev.preventDefault();
                  this.setState({
                    image: null
                  });
                }}>
          Detach
        </button>
      );
    }

    const imageModal = (
      <Modal ref="pickerModal">
        <ImagesList onSelect={this.addImage.bind(this)} />
      </Modal>
    );

    const typeButtons = this.state.types.map(type => (
      <label className={`form__checkbox-box broadcast--event-${type.id}`} key={`type-${type.id}`}>
        <input type="radio"
               name="type"
               value={type.id}
               className="form__input"
               defaultChecked={this.state.show.type === type.id}
        /> {type.title}
      </label>
    ));

    return (
      <React.Fragment>
        {this.message}
        <form method="post" className="form">

          <div className="t-person-edit">

            <div className="t-person-edit__detail">
              <h2 className="unit">Basic details</h2>

              <label className="form__label-row">
                Show name:
                <input type="text"
                       name="name"
                       className="form__input"
                       defaultValue={this.state.show.title}
                       required
                />
              </label>

              <label className="form__label-row">
                Tag line / Short Summary:
                <input type="text"
                       name="tagline"
                       className="form__input"
                       defaultValue={this.state.show.tagLine}
                />
              </label>

              <h3 className="unit">Show type</h3>
              <div className="form__checkbox-row">
                {typeButtons}
              </div>

            </div>
            <div className="t-person-edit__image">

              <h2 className="unit">Associated Image</h2>

              <input type="hidden"
                     name="image-id"
                     value={this.state.image ? this.state.image.id : 0}
              />
              <div className="t-person-edit__image-box">
                {image}
              </div>
              <div className="t-person-edit__image-actions">
                <button className="button"
                        onClick={(ev) => {
                          ev.preventDefault();
                          this.refs.pickerModal.open();
                        }}
                >
                  Choose
                </button>
                {detach}
              </div>

            </div>


            <div className="t-person-edit__full">
              <h2 className="unit">Full show detail</h2>
              <RichTextEditor
                initialContent={this.state.show.detail}
                fieldName="html-content"/>
            </div>

            <div className="t-person-edit__submit">
              <button className="button" type="submit">Save</button>
            </div>
          </div>
        </form>
        {imageModal}
      </React.Fragment>
    );
  }
}

export default ShowDetail;
