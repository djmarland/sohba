import * as React from "react";

class Modal extends React.Component {
  constructor(props) {
    super(props);
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
              {this.props.children}
            </div>
          </div>
        </div>
      );
    }

    return modal;
  }
}

export default Modal;
