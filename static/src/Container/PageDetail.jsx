import * as React from "react";

class PageDetail extends React.Component {
  state = {};

  componentDidMount() {
    this.setState({
      page: window.HBAContent.page,
      specialType: window.HBAContent.page.specialType || ""
    });
  }

  changeType(event) {
    this.setState({
      specialType: event.target.value,
    })
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
                 required
          />
        </label>
      );
    }

    return (
      <div className="text--prose">
        <form method="post" className="form">
          <button type="submit" style={{position:"sticky", float: "right"}}>Save</button>
          <h2>Basic details</h2>

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
              <option value="">None (set a URL)</option>
              <option value="home">Home</option>
              <option value="requests">Requests</option>
              <option value="sports">Sports</option>
              <option value="ob">Outside Broadcasts</option>
              <option value="people">People</option>
            </select>
          </label>

          {pageUrl}

          <h2>Page content</h2>

          <label>Enter the content for the page
          <textarea>

          </textarea>
          </label>

          <h2>Navigation details</h2>

          <label>
            <input type="checkbox" /> Include in navigation
          </label>

          <label>
            Navigation Category
            <select name="special" onChange={this.changeType.bind(this)} value={this.state.specialType}>
              <option value="1">What's On</option>
            </select>
          </label>


          <label>
            Navigation Title
            <input type="text"
                   name="title"
                   defaultValue={this.state.page.navTitle}
                   required
            />
          </label>

          <label>
            Navigation Position
            <input type="number"
                   name="nav-position"
                   min="1"
                   defaultValue={this.state.page.navPosition}
                   required
            />
          </label>
        </form>

      </div>
    );
  }
}

export default PageDetail;
