import * as React from "react";
import { hydrate as ReactDomRender } from "react-dom";

import AdminHome from "./Container/AdminHome";
import PagesList from "./Container/PagesList";
import PageDetail from "./Container/PageDetail";

// static assets
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';
import "../scss/app.scss";
import "../img";

// // find out if this page has an app we need to load
const appContainer = document.getElementById("app");
if (appContainer) {
  switch (appContainer.dataset.container) {
    case "AdminHome":
      ReactDomRender(<AdminHome />, appContainer);
      break;
    case "PagesList":
      ReactDomRender(<PagesList />, appContainer);
      break;
    case "PageDetail":
      ReactDomRender(<PageDetail />, appContainer);
      break;
  }
}
