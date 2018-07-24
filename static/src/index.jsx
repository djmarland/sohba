import * as React from "react";
import { render as ReactDomRender } from "react-dom";

import AdminHome from "./Pages/AdminHome";
import ImagesList from "./Pages/ImagesList";
import PagesList from "./Pages/PagesList";
import PageDetail from "./Pages/PageDetail";
import PeopleList from "./Pages/PeopleList";
import PersonDetail from "./Pages/PersonDetail";
import ShowsList from "./Pages/ShowsList";

// static assets
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';
import "../scss/app.scss";
import "../img";
import ShowDetail from "./Pages/ShowDetail";

// // find out if this page has an app we need to load
const appContainer = document.getElementById("app");
if (appContainer) {
  switch (appContainer.dataset.container) {
    case "AdminHome":
      ReactDomRender(<AdminHome />, appContainer);
      break;
    case "ImagesList":
      ReactDomRender(<ImagesList />, appContainer);
      break;
    case "PagesList":
      ReactDomRender(<PagesList />, appContainer);
      break;
    case "PageDetail":
      ReactDomRender(<PageDetail />, appContainer);
      break;
    case "PeopleList":
      ReactDomRender(<PeopleList />, appContainer);
      break;
    case "PersonDetail":
      ReactDomRender(<PersonDetail />, appContainer);
      break;
    case "ShowsList":
      ReactDomRender(<ShowsList />, appContainer);
      break;
    case "ShowDetail":
      ReactDomRender(<ShowDetail />, appContainer);
      break;
  }
}
