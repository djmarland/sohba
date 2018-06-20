import * as React from "react";
import RichTextEditor from "./RichTextEditor";

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
        <div className="message message--success">
          {window.HBAContent.messageOk}
        </div>
      );
    } else if (window.HBAContent.messageFail) {
      this.message = (
        <div className="message message--error">
          {window.HBAContent.messageFail}
        </div>
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
        <label>
          Page URL (after www.sohba.org/)
          <input type="text"
                 name="url"
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
          <label>
            Navigation Category
            <select name="nav-category"
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

          <label>
            Navigation Position
            <input type="number"
                   name="nav-position"
                   min="1"
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

              <h2>Basic details</h2>

              <p className="t-page-edit__pop">
                <a href={`/${this.state.page.legacyId}`}
                   target="_blank">View page ⇗</a>
              </p>

              <label>
                Full Page Title
                <input type="text"
                       name="title"
                       defaultValue={this.state.page.title}
                       required
                />
              </label>

              <label>
                Special Page type
                <select name="special" onChange={this.changeType.bind(this)} value={this.state.specialType}>
                  <option value="">--None (set a URL)--</option>
                  {specialPages}
                </select>
              </label>

              {pageUrl}
            </div>
            <div className="t-page-edit__nav">

              <h2>Navigation details</h2>

              <label>
                <input type="checkbox"
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
              <h2>Page content</h2>
              <label>Enter the content for the page</label>
              <RichTextEditor
                initialContent={this.state.page.htmlContent}
                fieldName="html-content"/>
            </div>
            <div className="t-page-edit__content">
              <h2>Legacy Page content</h2>
              <label>Enter the content for the page (legacy format - to be replaced)
                <textarea
                  name="content"
                  defaultValue={this.state.page.legacyContent}/>
              </label>
            </div>
            <div className="t-page-edit__submit">
              <button type="submit">Save</button>
            </div>
          </div>
        </form>
      </React.Fragment>
    );
  }
}

export default PageDetail;
