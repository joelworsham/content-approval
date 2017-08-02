import {schema, arrayOf} from 'normalizr';

export const workflow = new schema.Entity('workflows', {}, {
    processStrategy: (value) => ({
        id: value.id,
        fields: {
            title: value.title.rendered,
            post_types: value.post_types,
            submission_users: value.submission_users,
            submission_roles: value.submission_roles,
            approval_users: value.approval_users,
            approval_roles: value.approval_roles,
        }
    })
});

export const arrayOfWorkflows = new schema.Array(workflow);