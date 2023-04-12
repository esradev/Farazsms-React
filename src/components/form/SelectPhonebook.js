import { useState } from "react";
import AsyncCreatableSelect from "react-select/async-creatable";

const createOption = (label) => ({
  label,
  value: label.toLowerCase().replace(/\W/g, ""),
});

const loadPhonebooks = async () => {
  try {
    const phonebooks = await farazsmsJsObject.getPhonebooks;
    console.log(phonebooks);
    if (phonebooks.data.length === 0) {
      setOptions([]);
    } else {
      const phonebooksArrayObject = phonebooks.data.map(({ id, title }) => ({
        label: title,
        value: id,
      }));
      setOptions(phonebooksArrayObject);
    }
  } catch (e) {
    console.log(e);
  }
};

function SelectPhonebook() {
  const [isLoading, setIsLoading] = useState(false);
  const [options, setOptions] = useState();
  const [value, setValue] = useState(null);

  const handleCreate = (inputValue) => {
    setIsLoading(true);
    setTimeout(() => {
      const newOption = createOption(inputValue);
      setIsLoading(false);
      setOptions((prev) => [...prev, newOption]);
      setValue(newOption);
    }, 1000);
  };

  return (
    <AsyncCreatableSelect
      cacheOptions
      defaultOptions
      loadOptions={loadPhonebooks}
      isClearable
      isDisabled={isLoading}
      isLoading={isLoading}
      onChange={(newValue) => setValue(newValue)}
      onCreateOption={handleCreate}
      options={options}
      value={value}
    />
  );
}

export default SelectPhonebook;
