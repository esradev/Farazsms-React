import { useState, useEffect } from "react";
import CreatableSelect from "react-select/creatable";
import AxiosWp from "../function/AxiosWp";

function SelectPhonebook({
  options,
  dispatchNoPhonebooks,
  dispatchAllPhonebooks,
}) {
  const [isLoading, setIsLoading] = useState(false);
  const [value, setValue] = useState("");
  const [count, setCount] = useState(0);

  const addPhonebook = async (label) => {
    const res = await AxiosWp.post("/farazsms/v1/add_phonebook", {
      label,
    });
    console.log(res);
    if (res) {
      setCount(count++);
    }
  };

  const handleCreate = (inputValue) => {
    setIsLoading(true);
    setTimeout(() => {
      const newOption = addPhonebook(inputValue).then((res) =>
        setIsLoading(false)
      );
    }, 1000);
  };

  useEffect(() => {
    const getPhonebooks = async () => {
      const arr = [];
      const phonebooks = await farazsmsJsObject.getPhonebooks;
      if (phonebooks.length === 0) {
        dispatchNoPhonebooks();
      } else {
        phonebooks.map((phonebook) => {
          return arr.push({ value: phonebook.id, label: phonebook.title });
        });
        dispatchAllPhonebooks(arr);
      }
    };
    getPhonebooks();
  }, [count]);

  return (
    <CreatableSelect
      placeholder="Select a phonebook | Create a new phonebook by typing it's name"
      isClearable
      isDisabled={isLoading}
      isLoading={isLoading}
      onChange={(newValue) => setValue(newValue)}
      onCreateOption={handleCreate}
      options={options}
      value={value}
      noOptionsMessage={() => "There is no phonebook."}
    />
  );
}

export default SelectPhonebook;
