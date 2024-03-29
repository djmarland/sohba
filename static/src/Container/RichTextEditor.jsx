import * as React from "react";
import { EditorState, convertToRaw, ContentState } from "draft-js";
import { Editor } from "react-draft-wysiwyg";
import draftToHtml from "draftjs-to-html";
import htmlToDraft from "html-to-draftjs";

import RichTextImagePicker from "./RichTextImagePicker";

const toolbarOptions = {
  options: ["inline", "blockType", "list", "textAlign", "link", "history"],
  inline: {
    options: ["bold", "italic", "underline", "strikethrough"],
  },
  blockType: {
    inDropdown: true,
    options: ["Normal", "H2", "H3", "H4", "H5", "H6", "Blockquote"],
  },
  textAlign: {
    inDropdown: false,
    options: ["left", "center", "right"],
  },
  list: {
    options: ["unordered", "ordered"],
  },
};

const getRaw = (content) => {
  let htmlContent = draftToHtml(convertToRaw(content));
  htmlContent = htmlContent.trim();
  if (htmlContent === "<p></p>") {
    htmlContent = "";
  }
  return htmlContent;
};

class RichTextEditor extends React.Component {
  constructor(props) {
    super(props);
    const html = props.initialContent || "";
    const contentBlock = htmlToDraft(html);
    if (contentBlock) {
      const contentState = ContentState.createFromBlockArray(
        contentBlock.contentBlocks
      );
      const editorState = EditorState.createWithContent(contentState);
      this.state = {
        editorState,
      };
    }
  }

  onEditorStateChange(editorState) {
    this.setState({
      editorState,
    });
  }

  render() {
    const { editorState } = this.state;
    return (
      <React.Fragment>
        <Editor
          editorState={editorState}
          wrapperClassName="rich-text"
          toolbarClassName="rich-text__toolbar"
          editorClassName="rich-text__editor"
          toolbar={toolbarOptions}
          toolbarCustomButtons={[<RichTextImagePicker />]}
          onEditorStateChange={this.onEditorStateChange.bind(this)}
        />
        <textarea
          readOnly
          className="hidden--visually"
          name={this.props.fieldName}
          value={getRaw(editorState.getCurrentContent())}
        />
      </React.Fragment>
    );
  }
}

export default RichTextEditor;
