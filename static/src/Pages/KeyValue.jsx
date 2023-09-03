import * as React from "react";
import RichTextEditor from "../Container/RichTextEditor";
import Message from "../Components/Message";

class KeyValue extends React.Component {
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
      content: window.HBAContent.content,
    });
  }

  render() {
    if (!this.state.content) {
      return null;
    }

    let valueEditor = null;
    if (this.state.content.isRichText) {
      valueEditor = (
        <React.Fragment>
          <label className="form__label">Value</label>
          <RichTextEditor
            initialContent={this.state.content.richContent}
            fieldName="html-content"
          />
        </React.Fragment>
      );
    } else {
      valueEditor = (
        <label className="form__label-row">
          Value
          <input
            type="text"
            name="value"
            className="form__input"
            defaultValue={this.state.content.simpleContent}
            required
          />
        </label>
      );
    }

    return (
      <React.Fragment>
        {this.message}
        <form method="post" className="form">
          <label className="form__label-row">
            Description
            <input
              type="text"
              name="description"
              className="form__input"
              defaultValue={this.state.content.description}
              required
            />
          </label>
          {valueEditor}
          <div className="t-page-edit__submit">
            <button className="button" type="submit">
              Save
            </button>
          </div>
        </form>
      </React.Fragment>
    );
  }
}

export default KeyValue;
