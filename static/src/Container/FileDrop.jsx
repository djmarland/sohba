import React from 'react';
import Message from '../Components/Message';
import UploadIcon from "../Components/Icons/UploadIcon";

export default class FileDrop extends React.Component {
  constructor() {
    super();
    this.state = {
      isDragActive: false,
      messageType : null,
      messageText : null
    };
  }

  componentDidMount() {
    this.enterCounter = 0;
  }

  handleClick() {
    this.refs.fileInputEl.value = null;
    this.refs.fileInputEl.click();
  }

  handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    return false;
  }

  handleDrop(e) {
    e.preventDefault();

    // Reset the counter along with the drag on a drop.
    this.enterCounter = 0;

    this.setState({
      isDragActive: false,
      isDragReject: false,
      messageType : null,
      messageText : null
    });

    const droppedFiles = e.dataTransfer ? e.dataTransfer.files : e.target.files;
    if (droppedFiles.length > 1) {
      this.setState({
        messageType : Message.TYPE_ERROR,
        messageText : 'Only upload one file at a time'
      });
      return;
    }

    this.props.onFileReceived(droppedFiles[0]);
  }

  handleDragEnter(e) {
    e.preventDefault();

    // Count the dropzone and any children that are entered.
    ++this.enterCounter;

    this.setState({
      isDragActive: true,
      messageType : null,
      messageText : null
    });
  }

  handleDragLeave(e) {
    e.preventDefault();

    // Only deactivate once the dropzone and all children was left.
    if (--this.enterCounter > 0) {
      return;
    }

    this.setState({
      isDragActive: false,
    });
  }

  render() {
    let elClass = 'filedrop unit';
    if (this.state.isDragActive) {
      elClass += ' filedrop--drag';
    }

    return (
      <div className="unit">
        <div className={elClass}
             onClick={this.handleClick.bind(this)}
             onDragEnter={this.handleDragEnter.bind(this)}
             onDragLeave={this.handleDragLeave.bind(this)}
             onDrop={this.handleDrop.bind(this)}
             onDragOver={this.handleDragOver.bind(this)}
        >
          <span className="filedrop__icon">
            <UploadIcon />
          </span>
          <div className="filedrop__text">
            Drag and drop file (or click to choose).
          </div>
          <input ref="fileInputEl" style={ {display: 'none'} } type="file" onChange={this.handleDrop.bind(this)} />
        </div>
        <Message
          message={this.state.messageText}
          type={this.state.messageType}
        />
      </div>

    )
  }
}