import * as React from "react";
import { EditorState, convertToRaw, ContentState } from 'draft-js';
import { Editor } from "react-draft-wysiwyg";
import draftToHtml from 'draftjs-to-html';
import htmlToDraft from 'html-to-draftjs';

const toolbarOptions = {
  options: ["inline", "blockType", "list", "link", "image", "history"],
  inline: {
    options: ["bold", "italic", "underline", "strikethrough"]
  },
  blockType: {
    inDropdown: true,
    options: ["Normal", "H2", "H3", "H4", "H5", "H6", "Blockquote"]
  },
  list: {
    options: ["unordered", "ordered"]
  },
  image: {
    urlEnabled: true,
    uploadEnabled: true,
    alignmentEnabled: false,
    uploadCallback: undefined,
    previewImage: true,
    inputAccept: "image/gif,image/jpeg,image/jpg,image/png,image/svg",
    alt: { present: false, mandatory: false },
    defaultSize: {
      height: "auto",
      width: "auto"
    }
  }
};

const getRaw = content => {
  let htmlContent = draftToHtml(convertToRaw(content));
  htmlContent = htmlContent.trim();
  if (htmlContent === '<p></p>') {
    htmlContent = '';
  }
  return htmlContent;
};

class RichTextEditor extends React.Component {
  constructor(props) {
    super(props);
    const html = props.initialContent || '';
    const contentBlock = htmlToDraft(html);
    if (contentBlock) {
      const contentState = ContentState.createFromBlockArray(contentBlock.contentBlocks);
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
  };

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
