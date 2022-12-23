const [usercredit, setUsercredit] = useState();

const authentication_data = {
  headers: {
    Authorization: "AccessKey " + [state.apikey.value],
  },
};

async function handleSubmit(e) {
  e.preventDefault();
  try {
    // Get user info from IPPanel REST API
    const ippanelData = await Axios.get(
      "http://rest.ippanel.com/v1/user",
      authentication_data
    );
    if (ippanelData.data) {
      console.log(ippanelData.data.data.user);
    } else {
      console.log("there was an error");
    }

    // Get credit from IPPanel REST API
    const ippanelCredit = await Axios.get(
      "http://rest.ippanel.com/v1/credit",
      authentication_data
    );
    setUsercredit(ippanelCredit.data.data.credit);
  } catch (e) {
    console.log(e);
  }
}
