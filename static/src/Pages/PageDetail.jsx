import * as React from "react";
import RichTextEditor from "../Container/RichTextEditor";
import Message from "../Components/Message";

class PageDetail extends React.Component {
  state = {};
  categories = [];
  specialPages = [];
  urlRegex = "";
  message = null;

  componentDidMount() {
    this.specialPages = window.HBAContent.specialPages;
    this.categories = window.HBAContent.allCategories;
    this.urlRegex = window.HBAContent.urlRegex;

    if (window.HBAContent.messageOk) {
      this.message = (
        <Message
          message={window.HBAContent.messageOk}
          type={Message.TYPE_OK}
        />
      );
    } else if (window.HBAContent.messageFail) {
      this.message = (
        <Message
          message={window.HBAContent.messageFail}
          type={Message.TYPE_ERROR}
        />
      );
    }

    this.setState({
      page: window.HBAContent.page,
      specialType: window.HBAContent.specialType || "",
      showNavigation: !!window.HBAContent.page.category
    });
  }

  onEditorStateChange() {

  }

  changeType(event) {
    this.setState({
      specialType: event.target.value
    });
  }

  render() {

    if (!this.state.page) {
      return null;
    }

    let pageUrl = null;
    if (!this.state.specialType) {
      pageUrl = (
        <label className="form__label-row">
          Page URL (after www.sohba.org/)
          <input type="text"
                 name="url"
                 className="form__input"
                 defaultValue={this.state.page.urlPath}
                 pattern={this.urlRegex}
                 required
          />
        </label>
      );
    }

    const categories = this.categories.map(category => (
      <option key={category.legacyId} value={category.legacyId}>{category.title}</option>
    ));


    const specialPages = this.specialPages.map(page => (
      <option key={page.value} value={page.value}>{page.title}</option>
    ));

    let navigationContent = null;
    if (this.state.showNavigation) {
      navigationContent = (
        <React.Fragment>
          <label className="form__label-row">
            Navigation Category
            <select name="nav-category"
                    className="form__input"
                    defaultValue={this.state.page.category ?
                      this.state.page.category.legacyId : ""
                    }
                    required
            >
              {categories}
            </select>
          </label>

          {/*
          <label>
            Navigation Title Override
            <input type="text"
                   name="title"
                   placeholder="optional"
                   defaultValue={this.state.page.navTitle}
            />
          </label>
          */}

          <label className="form__label-row">
            Navigation Position
            <input type="number"
                   name="nav-position"
                   min="1"
                   className="form__input"
                   defaultValue={this.state.page.navPosition}
                   required
            />
          </label>
        </React.Fragment>
      );
    }

    return (
      <React.Fragment>
        {this.message}
        <form method="post" className="form">
          <div className="t-page-edit">
            <div className="t-page-edit__basic">

              <h2 className="unit">Basic details</h2>

              <p className="t-page-edit__pop">
                <a href={`/${this.state.page.legacyId}`}
                   target="_blank">View page ⇗</a>
              </p>

              <label className="form__label-row">
                Full Page Title
                <input type="text"
                       name="title"
                       className="form__input"
                       defaultValue={this.state.page.title}
                       required
                />
              </label>

              <label className="form__label-row">
                Special Page type
                <select name="special"
                        className="form__input"
                        onChange={this.changeType.bind(this)}
                        value={this.state.specialType}>
                  <option value="">--None (set a URL)--</option>
                  {specialPages}
                </select>
              </label>

              {pageUrl}
            </div>
            <div className="t-page-edit__nav">

              <h2 className="unit">Navigation details</h2>

              <label className="form__label-row">
                <input type="checkbox"
                       className="form__input"
                       checked={this.state.showNavigation}
                       onChange={() => {
                         this.setState({
                           showNavigation: !this.state.showNavigation
                         });
                       }}/> Include in navigation
              </label>

              {navigationContent}
            </div>
            <div className="t-page-edit__content">
              <h2 className="unit">Page content</h2>
              <label className="form__label">
                Enter the content for the page
              </label>
              <RichTextEditor
                initialContent={this.state.page.htmlContent}
                fieldName="html-content"/>
            </div>
            <div className="t-page-edit__content">
              <h2 className="unit">Legacy Page content</h2>
              <label className="form__label-row">
                Enter the content for the page (legacy format - to be replaced)
                <textarea
                  name="content"
                  className="form__input form__input--textarea"
                  defaultValue={this.state.page.legacyContent}/>
              </label>
            </div>
            <div className="t-page-edit__submit">
              <button className="button" type="submit">Save</button>
            </div>
          </div>
        </form>
      </React.Fragment>
    );
  }
}

export default PageDetail;
