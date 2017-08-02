import React from 'react';

import {
    FieldText,
    FieldSelect,
    FieldHidden,
    FieldHTML,
} from './fields';

import {closeWorkflowModal} from '../actions/workflow-modal';
import {addWorkflow, editWorkflow} from '../actions/workflows';

const workflowSchema = WFM_ManageWorkflows['workflowSchema'];
const l10n           = WFM_ManageWorkflows['l10n'];
const APINonce       = WFM_ManageWorkflows['nonce'];

/**
 * Loads users for the options.
 *
 * @since {{VERSION}}
 *
 * @return {[]}
 */
const loadUsers = (input) => (

    fetch('/wp-json/wp/v2/users/', {
        method: 'GET',
        credentials: 'same-origin',
        headers: new Headers({
            'Content-Type': 'application/json',
            'X-WP-Nonce': APINonce,
        }),
        body: {
            search: input,
        }
    })
        .then((response) => {

            return response.json()

        }).then((json) => {

        let options = [];

        json.map((user) => {

            options.push({
                label: user.name,
                value: user.id,
            })
        })

        return {options: options};
    })
)

/**
 * Returns the proper field component for the field schema.
 *
 * @since {{VERSION}}
 *
 * @param {{}} fieldSchema Schema of the workflow fields.
 * @param {mixed} value Field value.
 * @param {func} onChange Callback for changing.
 */
const getWorkflowField = (fieldSchema, value = false, error = false, onChange = false) => {

    switch ( fieldSchema.type ) {

        case 'text':

            return <FieldText
                key={fieldSchema.name}
                {...fieldSchema}
                onChange={onChange}
                value={value}
                error={error}
            />

        case 'select':

            if ( fieldSchema.loadOptions ) {

                switch ( fieldSchema.loadOptions.type ) {

                    case 'users':

                        fieldSchema.loadOptions = loadUsers;
                        break;
                }
            }

            return <FieldSelect
                key={fieldSchema.name}
                {...fieldSchema}
                value={value}
                onChange={onChange}
                error={error}
            />

        case 'hidden':

            return <FieldHidden
                key={fieldSchema.name}
                {...fieldSchema}
                value={value}
                onChange={onChange}
            />

        case 'html':

            return <FieldHTML
                key={fieldSchema.name}
                {...fieldSchema}
            />

        default:

            return l10n['field_type_not_found'].replace('%s', fieldSchema.type);
    }
}

/**
 * Container for the Workflow edit/add form.
 *
 * @since {{VERSION}}
 */
class WorkflowForm extends React.Component {

    constructor(props, context) {

        super(props);

        const {store}   = context;
        const state     = store.getState();
        const fields    = state.workflowModal.fields;
        let formFields  = {};

        workflowSchema.map(fieldSchema => {
            formFields[fieldSchema.name] = fields[fieldSchema.name] || fieldSchema.default || '';
        });

        this.state = {
            fieldErrors: {},
            formValues: formFields,
        };

        this.submit = this.submit.bind(this);
    }

    componentDidMount() {

        const {store} = this.context;

        this.unsubscribe = store.subscribe(() => {
            this.forceUpdate();
        });
    }

    componentWillUnmount() {

        this.unsubscribe();
    }

    /**
     * Determines of a value, of many types, is falsey.
     *
     * @since {{VERSION}}
     *
     * @param {mixed} value
     * @returns {boolean}
     */
    isFalsey(value) {

        switch ( typeof value ) {

            case 'string':

                return value === '';

            case 'array':

                return value.length === 0;

            case 'object':

                return Object.keys(value).length === 0;

            default:

                return !value;
        }
    }

    /**
     * Validates all form fields.
     *
     * @since {{VERSION}}
     *
     * @returns {{}}
     */
    validateFields() {

        let errors = {};

        workflowSchema.map(schema => {

            // Required
            if ( schema.required ) {

                switch ( typeof schema.required ) {

                    case 'object':

                        // Put self in check
                        let check = [...schema.required.fields];
                        check.push(schema.name);

                        // Get list of field names in check
                        let fieldNames = [];

                        // This is fun. Loops through all required fields, then loops through all schema to find
                        // matching field schema to obtain the label and add it to the list.
                        check.map(field => {
                            workflowSchema.map(_schema => {
                                if ( (field === _schema.name) && _schema.label ) {
                                    fieldNames.push(_schema.label);
                                }
                            });
                        });

                        switch ( schema.required.relation ) {

                            case 'AND':

                                // If any aren't filled, invalidate
                                check.map(field => {

                                    if ( !this.isFalsey(this.state.formValues[field]) ) {

                                        errors[schema.name] = l10n['field_required_and_message']
                                            .replace('%s', fieldNames.join(', '));
                                    }
                                });

                            case 'OR':
                            default:

                                // Default to invalid unless at least one is filled
                                let fieldInvalid = true;

                                check.map(field => {

                                    if ( !this.isFalsey(this.state.formValues[field]) ) {

                                        fieldInvalid = false;
                                    }
                                });

                                if ( fieldInvalid ) {

                                    errors[schema.name] = l10n['field_required_or_message']
                                        .replace('%s', fieldNames.join(', '));
                                    ;
                                }
                        }

                        break

                    default:

                        if ( this.isFalsey(this.state.formValues[schema.name]) ) {

                            errors[schema.name] = l10n['field_required_message'];
                        }
                }
            }
        });

        this.setState({fieldErrors: errors});

        return errors;
    }

    /**
     * Submits the form data.
     *
     * @since {{VERSION}}
     */
    submit() {

        const {store} = this.context;
        const state   = store.getState();
        const errors  = this.validateFields();

        // Determine if no errors
        // if ( !this.isFalsey(errors) ) {
        //
        //     return;
        // }

        if ( state.workflowModal.id !== -1 ) {

            store.dispatch(
                editWorkflow(
                    state.workflowModal.id,
                    this.state.formValues,
                )
            );

        } else {

            store.dispatch(
                addWorkflow(this.state.formValues)
            );
        }

        // TODO Loading animation and error handling for workflow adding/editing
        store.dispatch(
            closeWorkflowModal()
        );
    }

    /**
     * Renders the component.
     *
     * @since {{VERSION}}
     *
     * @returns {XML}
     */
    render() {

        const {store}   = this.context;
        const state     = store.getState();
        const fields    = state.workflowModal.fields;
        const id        = state.workflowModal.id;
        const component = this;

        console.log(fields);

        return (
            <form>
                {workflowSchema.map(fieldSchema => (
                    getWorkflowField(
                        fieldSchema,
                        fields[fieldSchema.name] || fieldSchema.default || false,
                        this.state.fieldErrors[fieldSchema.name] || false,
                        (value) => {

                            component.setState((prevState) => {

                                prevState.formValues[fieldSchema.name] = value;

                                return prevState;
                            })
                        }
                    )
                ))}
            </form>
        );
    }
}

WorkflowForm.contextTypes = {
    store: React.PropTypes.object,
};

export default WorkflowForm;