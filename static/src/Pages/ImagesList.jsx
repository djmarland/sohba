import * as React from "react";
import FileDrop from "../Container/FileDrop";
import Message from "../Components/Message";

const isCorrectFileType = file => (/^(jpg|jpeg|png)$/i).test(file);

class Container extends React.Component {

  state = {
    message: null,
    loading: false,
    images: [],
  };

  componentDidMount() {
    this.setState({
      message: window.HBAContent.message || null,
      images: window.HBAContent.images || [],
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
          },
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
              message: data.message.text,
              type: data.type
            },
            images : data.images,
          });
        })
        .catch(error => {
          console.log(error);
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
          message={this.state.message.text}
          type={this.state.message.type}
        />
      )
    }

    const images = this.state.images.map(image => (
      <p key={image.id}>
        <img src={image.src} />
      </p>
    ));

    return (
      <React.Fragment>
        {message}
        {fileDrop}
        {images}
      </React.Fragment>
    );
  }
}

export default Container;
