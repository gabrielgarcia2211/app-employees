import React from "react";

const Spinner = () => {
  return (
    <div className="spinner-border text-primary" role="status" style={{ marginLeft: "10px", marginTop: "10px" }}>
      <span className="sr-only">.</span>
    </div>
  );
};

export default Spinner;
