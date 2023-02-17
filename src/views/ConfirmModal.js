import React, { useCallback, useEffect } from "react";
import useConfirm from "../useConfirm";
import Button from "react-bootstrap/Button";
import Modal from "react-bootstrap/Modal";

const ConfirmModal = () => {
  const { prompt = "", isOpen = false, proceed, cancel } = useConfirm();
  return (
    <Modal
      {...props}
      isOpen={isOpen}
      size="lg"
      aria-labelledby="contained-modal-title-vcenter"
      centered
    >
      <Modal.Header closeButton>
        <Modal.Title id="contained-modal-title-vcenter">Confirm</Modal.Title>
      </Modal.Header>
      <Modal.Body>{prompt}</Modal.Body>
      <Modal.Footer>
        <Button onClick={proceed}>Ok</Button>
        <Button onClick={cancel}>Cancel</Button>
      </Modal.Footer>
    </Modal>
  );
};
export default ConfirmModal;
