import React from "react";
import TickIcon from "./Icons/TickIcon";
import WarningIcon from "./Icons/WarningIcon";
import InfoIcon from "./Icons/InfoIcon";
import ErrorIcon from "./Icons/ErrorIcon";

class Message extends React.Component {
  static get TYPE_OK() {
    return "ok";
  }
  static get TYPE_WARNING() {
    return "warning";
  }
  static get TYPE_ERROR() {
    return "error";
  }
  static get TYPE_INFO() {
    return "info";
  }
  constructor() {
    super();
  }

  render() {
    if (!this.props.message) {
      return <MessageNone />;
    }
    switch (this.props.type) {
      case Message.TYPE_OK:
        return <MessageOk message={this.props.message} />;
      case Message.TYPE_WARNING:
        return <MessageWarning message={this.props.message} />;
      case Message.TYPE_ERROR:
        return <MessageError message={this.props.message} />;
      case Message.TYPE_INFO:
      default:
        return <MessageInfo message={this.props.message} />;
    }
  }
}

class MessageOk extends React.Component {
  render() {
    return (
      <div className="message message--ok">
        <span className="icon-text">
          <span className="icon-text__icon">
            <TickIcon />
          </span>
          <span className="icon-text__text">{this.props.message}</span>
        </span>
      </div>
    );
  }
}

class MessageWarning extends React.Component {
  render() {
    return (
      <div className="message message--warning">
        <span className="icon-text">
          <span className="icon-text__icon">
            <WarningIcon />
          </span>
          <span className="icon-text__text">{this.props.message}</span>
        </span>
      </div>
    );
  }
}

class MessageError extends React.Component {
  render() {
    return (
      <div className="message message--error">
        <span className="icon-text">
          <span className="icon-text__icon">
            <ErrorIcon />
          </span>
          <span className="icon-text__text">{this.props.message}</span>
        </span>
      </div>
    );
  }
}

class MessageInfo extends React.Component {
  render() {
    return (
      <div className="message message--info">
        <span className="icon-text">
          <span className="icon-text__icon">
            <InfoIcon />
          </span>
          <span className="icon-text__text">{this.props.message}</span>
        </span>
      </div>
    );
  }
}

class MessageNone extends React.Component {
  render() {
    return null;
  }
}

export default Message;
