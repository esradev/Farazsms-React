import { useState, useEffect } from "react";
import CreatableSelect from "react-select/creatable";
import AxiosWp from "../function/AxiosWp";

function SelectPhonebook({
  isMulti,
  value,
  options,
  onChange,
  dispatchNoPhonebooks,
  dispatchAllPhonebooks,
}) {
  const [isLoading, setIsLoading] = useState(false);
  const [phonebookCount, setPhonebookCount] = useState(0); // <-- Add new state variable

  const addPhonebook = async (label) => {
    setIsLoading(true);
    const res = await AxiosWp.post("/farazsms/v1/add_phonebook", { label });
    setIsLoading(false);
    console.log(res);
    if (res) {
      const newPhonebook = JSON.parse(res.data.body).data;
      const newOption = { value: newPhonebook.id, label: newPhonebook.title };
      dispatchAllPhonebooks((prevOptions) => [...prevOptions, newOption]);
      onChange(newOption); // <-- Set new phonebook as the selected option
      setPhonebookCount((prevCount) => prevCount + 1); // <-- Update phonebook count state to force re-render
    }
  };

  const handleCreate = (inputValue) => {
    setIsLoading(true);
    addPhonebook(inputValue);
  };

  useEffect(() => {
    const getPhonebooks = async () => {
      const arr = [];

      const phonebooks = await farazsmsJsObject.getPhonebooks;
      console.log(phonebooks);
      if (!phonebooks || phonebooks.length === 0) {
        dispatchNoPhonebooks();
      } else {
        phonebooks.map((phonebook) => {
          return arr.push({ value: phonebook.id, label: phonebook.title });
        });
        dispatchAllPhonebooks(arr);
      }
    };
    getPhonebooks();
  }, [phonebookCount]); // <-- Reload useEffect when phonebook count changes

  return (
    <CreatableSelect
      isMulti={isMulti}
      placeholder="Select a phonebook | Create a new phonebook by typing it's name"
      isClearable
      isDisabled={isLoading}
      isLoading={isLoading}
      onChange={onChange}
      onCreateOption={handleCreate}
      options={options}
      value={value}
      noOptionsMessage={() => "There is no phonebook."}
    />
  );
}

export default SelectPhonebook;
