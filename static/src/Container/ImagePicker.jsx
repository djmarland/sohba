import * as React from "react";
import { EditorState, Modifier } from 'draft-js';
import ImagesList from "./ImagesList";
import * as AtomicBlockUtils from "draft-js/lib/AtomicBlockUtils";

class ImagePicker extends React.Component {
  constructor() {
    super();
    this.state = {
      isOpen: false,
    };
  }

  open() {
    this.setState({
      isOpen: true
    });
  }

  close() {
    this.setState({
      isOpen: false
    });
  }

  addImage(image) {
    const { editorState, onChange } = this.props;
    const entityData = {
      src : image.src,
      height : "auto",
      width : "auto",
    };

    const entityKey = editorState
      .getCurrentContent()
      .createEntity('IMAGE', 'MUTABLE', entityData)
      .getLastCreatedEntityKey();

    const newEditorState = AtomicBlockUtils.insertAtomicBlock(
      editorState,
      entityKey,
      ' ',
    );
    onChange(newEditorState);
    this.close();
  }

  addStar() {
    const { editorState, onChange } = this.props;
    const contentState = Modifier.replaceText(
      editorState.getCurrentContent(),
      editorState.getSelection(),
      '⭐',
      editorState.getCurrentInlineStyle(),
    );
    onChange(EditorState.push(editorState, contentState, 'insert-characters'));
  };

  render() {
    let modal = null;
    if (this.state.isOpen) {
      modal = (
        <div className="modal">
          <div className="modal__panel">
            <div className="modal__close">
              <button className="button"
                      onClick={this.close.bind(this)}
              >
                Close
              </button>
            </div>
            <div className="modal__content">
              <ImagesList onSelect={this.addImage.bind(this)} />
            </div>
          </div>
        </div>
      );
    }

    return (
      <div className="rdw-link-wrapper" aria-label="rdw-link-control">
        <div className="rdw-option-wrapper" title="Image"
             onClick={this.open.bind(this)}>
          <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjE1cHgiIGhlaWdodD0iMTRweCIgdmlld0JveD0iMCAwIDE1IDE0IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA0MC4zICgzMzgzOSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+aW1hZ2U8L3RpdGxlPgogICAgPGRlc2M+Q3JlYXRlZCB3aXRoIFNrZXRjaC48L2Rlc2M+CiAgICA8ZGVmcz48L2RlZnM+CiAgICA8ZyBpZD0iUGFnZS0xIiBzdHJva2U9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIgZmlsbD0ibm9uZSIgZmlsbC1ydWxlPSJldmVub2RkIj4KICAgICAgICA8ZyBpZD0iaW1hZ2UiIGZpbGw9IiMwMDAwMDAiPgogICAgICAgICAgICA8ZyBpZD0iQ2FwYV8xIj4KICAgICAgICAgICAgICAgIDxnIGlkPSJHcm91cCI+CiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTE0Ljc0MTM3OTMsMCBMMC4yNTg2MjA2OSwwIEMwLjExNTg2MjA2OSwwIDAsMC4xMzYwNDM0NzggMCwwLjMwNDM0NzgyNiBMMCwxMy42OTU2NTIyIEMwLDEzLjg2Mzk1NjUgMC4xMTU4NjIwNjksMTQgMC4yNTg2MjA2OSwxNCBMMTQuNzQxMzc5MywxNCBDMTQuODg0MTM3OSwxNCAxNSwxMy44NjM5NTY1IDE1LDEzLjY5NTY1MjIgTDE1LDAuMzA0MzQ3ODI2IEMxNSwwLjEzNjA0MzQ3OCAxNC44ODQxMzc5LDAgMTQuNzQxMzc5MywwIEwxNC43NDEzNzkzLDAgWiBNMTQuNDgyNzU4NiwxMy4zOTEzMDQzIEwwLjUxNzI0MTM3OSwxMy4zOTEzMDQzIEwwLjUxNzI0MTM3OSwwLjYwODY5NTY1MiBMMTQuNDgyNzU4NiwwLjYwODY5NTY1MiBMMTQuNDgyNzU4NiwxMy4zOTEzMDQzIEwxNC40ODI3NTg2LDEzLjM5MTMwNDMgWiIgaWQ9IlNoYXBlIj48L3BhdGg+CiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTQuMTM3OTMxMDMsNi43Mzc2NTIxNyBDNC45MzIxNTUxNyw2LjczNzY1MjE3IDUuNTc4MTg5NjYsNS45NzczOTEzIDUuNTc4MTg5NjYsNS4wNDMwNDM0OCBDNS41NzgxODk2Niw0LjEwODA4Njk2IDQuOTMyMTU1MTcsMy4zNDc4MjYwOSA0LjEzNzkzMTAzLDMuMzQ3ODI2MDkgQzMuMzQzNzA2OSwzLjM0NzgyNjA5IDIuNjk3NjcyNDEsNC4xMDgwODY5NiAyLjY5NzY3MjQxLDUuMDQyNzM5MTMgQzIuNjk3NjcyNDEsNS45NzczOTEzIDMuMzQzNzA2OSw2LjczNzY1MjE3IDQuMTM3OTMxMDMsNi43Mzc2NTIxNyBMNC4xMzc5MzEwMyw2LjczNzY1MjE3IFogTTQuMTM3OTMxMDMsMy45NTY1MjE3NCBDNC42NDY4OTY1NSwzLjk1NjUyMTc0IDUuMDYwOTQ4MjgsNC40NDQwODY5NiA1LjA2MDk0ODI4LDUuMDQyNzM5MTMgQzUuMDYwOTQ4MjgsNS42NDEzOTEzIDQuNjQ2ODk2NTUsNi4xMjg5NTY1MiA0LjEzNzkzMTAzLDYuMTI4OTU2NTIgQzMuNjI4OTY1NTIsNi4xMjg5NTY1MiAzLjIxNDkxMzc5LDUuNjQxNjk1NjUgMy4yMTQ5MTM3OSw1LjA0MzA0MzQ4IEMzLjIxNDkxMzc5LDQuNDQ0MzkxMyAzLjYyODk2NTUyLDMuOTU2NTIxNzQgNC4xMzc5MzEwMywzLjk1NjUyMTc0IEw0LjEzNzkzMTAzLDMuOTU2NTIxNzQgWiIgaWQ9IlNoYXBlIj48L3BhdGg+CiAgICAgICAgICAgICAgICAgICAgPHBhdGggZD0iTTEuODEwMzQ0ODMsMTIuMTczOTEzIEMxLjg3MDg2MjA3LDEyLjE3MzkxMyAxLjkzMTg5NjU1LDEyLjE0ODk1NjUgMS45ODEwMzQ0OCwxMi4wOTgxMzA0IEw2LjE5OTkxMzc5LDcuNzI3MDg2OTYgTDguODY0MjI0MTQsMTAuODYyMTczOSBDOC45NjUzNDQ4MywxMC45ODExNzM5IDkuMTI4NzkzMSwxMC45ODExNzM5IDkuMjI5OTEzNzksMTAuODYyMTczOSBDOS4zMzEwMzQ0OCwxMC43NDMxNzM5IDkuMzMxMDM0NDgsMTAuNTUwODI2MSA5LjIyOTkxMzc5LDEwLjQzMTgyNjEgTDcuOTg2NzI0MTQsOC45Njg4MjYwOSBMMTAuMzYxMTIwNyw1LjkwODkxMzA0IEwxMy4yNzM0NDgzLDkuMDUwNjk1NjUgQzEzLjM3ODcwNjksOS4xNjQyMTczOSAxMy41NDI0MTM4LDkuMTU1Njk1NjUgMTMuNjM4ODc5Myw5LjAzMTgyNjA5IEMxMy43MzUzNDQ4LDguOTA3OTU2NTIgMTMuNzI4MzYyMSw4LjcxNTMwNDM1IDEzLjYyMjg0NDgsOC42MDE3ODI2MSBMMTAuNTE5Mzk2Niw1LjI1Mzk1NjUyIEMxMC40Njg3MDY5LDUuMTk5NDc4MjYgMTAuNDAxMjA2OSw1LjE3MjM5MTMgMTAuMzMzMTg5Nyw1LjE3NDIxNzM5IEMxMC4yNjQ2NTUyLDUuMTc3ODY5NTcgMTAuMiw1LjIxMzQ3ODI2IDEwLjE1MzcwNjksNS4yNzMxMzA0MyBMNy42MjA3NzU4Niw4LjUzNzg2OTU3IEw2LjM5NDEzNzkzLDcuMDk0MzQ3ODMgQzYuMjk3NDEzNzksNi45ODA4MjYwOSA2LjE0Mjc1ODYyLDYuOTc1MDQzNDggNi4wNDAzNDQ4Myw3LjA4MDk1NjUyIEwxLjYzOTM5NjU1LDExLjY0MSBDMS41MzIwNjg5NywxMS43NTIwODcgMS41MjE3MjQxNCwxMS45NDQ0MzQ4IDEuNjE2MTIwNjksMTIuMDcwNzM5MSBDMS42NjczMjc1OSwxMi4xMzkyMTc0IDEuNzM4NzA2OSwxMi4xNzM5MTMgMS44MTAzNDQ4MywxMi4xNzM5MTMgTDEuODEwMzQ0ODMsMTIuMTczOTEzIFoiIGlkPSJTaGFwZSI+PC9wYXRoPgogICAgICAgICAgICAgICAgPC9nPgogICAgICAgICAgICA8L2c+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4="
               alt=""
          />
        </div>
        {modal}
      </div>
    );
  }
}

export default ImagePicker;
