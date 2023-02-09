/**
 * Import remote dependencies.
 */
import React from 'react'
// Used as const not import, for Loco translate plugin compatibility.
const __ = wp.i18n.__

/**
 * This component power the settings component.
 *
 * @since 2.0.0
 */

const SaveButton = (props) => {
  const { isSaving } = props
  return (
    <button type="submit" className="btn btn-primary mt-3" disabled={isSaving}>
      {__('Save Settings', 'farazsms')}
    </button>
  )
}

export default SaveButton
