import * as React from "react";
import FileDrop from "../Container/FileDrop";
import Message from "../Components/Message";
import TickIcon from "../Components/Icons/TickIcon";
import DeleteIcon from "../Components/Icons/DeleteIcon";

const isCorrectFileType = file => (/^(jpg|jpeg|png)$/i).test(file);

class Container extends React.Component {

  state = {
    message: null,
    loading: false,
    images: []
  };

  componentDidMount() {
    this.setState({
      message: window.HBAContent.message || null,
      images: window.HBAContent.images || []
    });
  }

  handleReceivedFile(file) {
    let reader = new FileReader();

    this.setState({ loading: true, message: null });
    reader.onload = e => {

      const res = e.currentTarget.result;
      const fileType = res.split(":")[1].split("/")[1].split(";")[0];
      if (!isCorrectFileType(fileType)) {
        this.setState({
          loading: false,
          message: {
            message: "That was not a valid image type (jpg/png)",
            type: Message.TYPE_ERROR
          }
        });
        return;
      }

      fetch("/admin/images", {
        method: "post",
        body: res,
        credentials: "same-origin"
      })
        .then(response => {
          return response.json();
        })
        .then(data => {
          this.setState({
            loading: false,
            message: {
              message: data.message.message,
              type: data.message.type
            },
            images: data.images
          });
        })
        .catch(error => {
          this.setState({
            loading: false,
            message: {
              message: "An error occurred",
              type: Message.TYPE_ERROR
            }
          });
        });
    };

    reader.readAsDataURL(file);
  }

  imageClick(image) {
    if (this.props.onSelect) {
      this.props.onSelect(image);
    }
  }

  renderImage(image) {

    let edit = null;
    if (this.props.allowEdit === true) {
      edit = (
        <div className="images__edit">
          <form method="post" className="form">
            <input
              type="hidden"
              name="update-image"
              value={image.id}
            />
            <label htmlFor={`field-title-${image.id}`}
                   className="hidden--visually"
            >
              Image title
            </label>
            <div className="form__row form__row--inline">
              <input
                id={`field-title-${image.id}`}
                type="text"
                name="image-title"
                className="form__input form__input--compact"
                defaultValue={image.title}
              />
              <button
                className="button button--icon"
                type="submit"
                title="Edit image title"
              >
                <TickIcon/>
              </button>
            </div>
          </form>
          <form
            method="post"
            onSubmit={e => {
              if (
                !window.confirm(`Are you sure you want to delete this image?`)
              ) {
                e.preventDefault();
              }
            }}>
            <input
              type="hidden"
              name="delete-image"
              value={image.id}
            />
            <button
              className="button button--icon button--danger"
              type="submit"
              title="Delete category"
            >
              <DeleteIcon/>
            </button>
          </form>
        </div>
      );
    }

    const modifier = (this.props.onSelect) ? 'images__image--selectable' : null;

    return (
      <li key={image.id} className="images__item">
        <div className={`images__image ${modifier}`}
             onClick={() => {this.imageClick(image)}}>
          <img src={image.src}/>
        </div>
        {edit}
      </li>
    );
  }

  render() {
    let fileDrop = null;
    if (this.state.loading) {
      fileDrop = (
        <Message type={Message.TYPE_INFO} message="Uploading..."/>
      );
    } else {
      fileDrop = (
        <FileDrop onFileReceived={this.handleReceivedFile.bind(this)}/>
      );
    }

    let message = null;
    if (this.state.message) {
      message = (
        <Message
          message={this.state.message.message}
          type={this.state.message.type}
        />
      );
    }

    const images = this.state.images.map(this.renderImage.bind(this));

    return (
      <React.Fragment>
        {message}
        {fileDrop}
        <div className="modal-content-target">
          <ul className="images">
            {images}
          </ul>
        </div>
      </React.Fragment>
    );
  }
}

export default Container;
