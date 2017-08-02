import React from 'react';

import WorkflowAdd from './workflow-add';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Presentational component for the header.
 *
 * @since {{VERSION}}
 *
 * @constructor
 */
const Header = () => (
    <header className="wfm-manage-workflows-header">
        <h1 className="wp-heading-inline">
            {l10n['manage_workflows']}
        </h1>

        <WorkflowAdd />

        <hr className="wp-header-end" />
    </header>
)

export default Header;