import React from 'react';

const l10n = WFM_ManageWorkflows['l10n'];

/**
 * Presentational component for showing a Workflow item in the workflows table.
 *
 * @since {{VERSION}}
 *
 * @param {func} onTitleClick Fires when clicking the title link.
 * @param {string} title The title text.
 * @constructor
 */
export const WorkflowRow = ({
    onTitleClick,
    onDeleteClick,
    title,
    isDeleting,
}) => (
    <tr className={(isDeleting ? 'wfm-workflow-deleting' : '')}>
        <td>
            <a href="#" onClick={onTitleClick}>
                {title}
            </a>
        </td>

        <td>
            <a href="#" onClick={onDeleteClick}>
                {l10n['delete']}
            </a>
        </td>
    </tr>
);