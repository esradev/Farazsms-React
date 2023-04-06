import { useEffect } from "react";

/**
 * Get phonebooks.
 * Used wp_remote_post() from the php, for avoid No 'Access-Control-Allow-Origin' header is present on the requested resource. error when send this request with axios
 *
 * @since 2.0.0
 */
function usePhonebooks(dispatchNoPhonebooks, dispatchAllPhonebooks) {
  useEffect(() => {
    async function getPhonebooks() {
      try {
        //farazsmsJsObject is declared on class-farazsms-settings.php under admin_enqueue_scripts function
        const phonebooks = await farazsmsJsObject.getPhonebooks;
        console.log(phonebooks);
        if (phonebooks.data.length === 0) {
          dispatchNoPhonebooks();
        } else {
          const phonebooksArrayObject = phonebooks.data.map(
            ({ id, title }) => ({
              label: title,
              value: id,
            })
          );
          dispatchAllPhonebooks(phonebooksArrayObject);
        }
      } catch (e) {
        console.log(e);
      }
    }

    getPhonebooks();
  }, []);
}

export default usePhonebooks;
