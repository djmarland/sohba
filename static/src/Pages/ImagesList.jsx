import * as React from "react";
import ImagesList from "../Container/ImagesList";

class Container extends React.Component {
  render() {
    return <ImagesList allowEdit={true} />;
  }
}

export default Container;
