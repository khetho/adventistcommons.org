import React from "react";
import {
    HydraAdmin,
    ResourceGuesser,
    ListGuesser,
    FieldGuesser,
    CreateGuesser,
    EditGuesser,
    InputGuesser,
    ShowGuesser,
} from "@api-platform/admin";
import {
    LongTextInput,
    ReferenceInput,
    ReferenceArrayInput,
    AutocompleteInput,
    AutocompleteArrayInput,
    SelectArrayInput
} from "react-admin";

import {
    Person,
    AccountBox,
    Work,
    Language,
    Widgets,
    PermMedia,
    Contacts,
    Translate,
    LibraryBooks,
} from '@material-ui/icons';
import parseHydraDocumentation from '@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation';
import { dataProvider as baseDataProvider, fetchHydra as baseFetchHydra  } from '@api-platform/admin';
import { Redirect } from 'react-router-dom';
import authProvider from './authProvider';

/**
 * Authentication
 */
const entryPoint = "http://localhost:8096/api";
const fetchHeaders = {'Authorization': `Bearer ${window.localStorage.getItem('token')}`};
const fetchHydra = (url, options = {}) => baseFetchHydra(url, {
    ...options,
    headers: new Headers(fetchHeaders),
});
const apiDocumentationParser = entrypoint => parseHydraDocumentation(entrypoint, { headers: new Headers(fetchHeaders) })
    .then(
        ({ api }) => ({api}),
        (result) => {
            switch (result.status) {
                case 401:
                    return Promise.resolve({
                        api: result.api,
                        customRoutes: [{
                            props: {
                                path: '/',
                                render: () => <Redirect to={`/login`}/>,
                            },
                        }],
                    });

                default:
                    return Promise.reject(result);
            }
        },
    );
const dataProvider = baseDataProvider(entryPoint, fetchHydra, apiDocumentationParser);

/**
 * customisations
 */
const UsersList = props => (
    <ListGuesser {...props}>
        <FieldGuesser source="firstName" />
        <FieldGuesser source="lastName" />
        <FieldGuesser source="email" />
        <FieldGuesser source="groups" />
        <FieldGuesser source="active" />
        <FieldGuesser source="createdOn" />
        <FieldGuesser source="lastLogin" />
    </ListGuesser>
);

const UsersCreate = props => (
    <CreateGuesser {...props}>
        <InputGuesser source="password" />
        <InputGuesser source="email" />
        <InputGuesser source="active" />
        <InputGuesser source="firstName" />
        <InputGuesser source="lastName" />
        <InputGuesser source="company" />
        <InputGuesser source="phone" />
        <InputGuesser source="location" />
        <LongTextInput source="bio" />
        <ReferenceArrayInput
            source="skills"
            reference="skills"
            label="Skills"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <SelectArrayInput optionText="name" />
        </ReferenceArrayInput>
        <ReferenceInput
            source="motherLanguage"
            reference="languages"
            label="Mother Language"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <AutocompleteInput optionText="name" />
        </ReferenceInput>
        <ReferenceArrayInput
            source="languages"
            reference="languages"
            label="Languages"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <AutocompleteArrayInput optionText="name" />
        </ReferenceArrayInput>
        <ReferenceArrayInput
            source="groups"
            reference="groups"
            label="Groups"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <SelectArrayInput optionText="name" />
        </ReferenceArrayInput>
    </CreateGuesser>
);

const UsersEdit = props => (
    <EditGuesser {...props}>
        <InputGuesser source="password" />
        <InputGuesser source="email" />
        <InputGuesser source="active" />
        <InputGuesser source="firstName" />
        <InputGuesser source="lastName" />
        <InputGuesser source="company" />
        <InputGuesser source="phone" />
        <InputGuesser source="location" />
        <LongTextInput source="bio" />
        <ReferenceArrayInput
            source="skills"
            reference="skills"
            label="Skills"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <SelectArrayInput optionText="name" />
        </ReferenceArrayInput>
        <ReferenceInput
            source="motherLanguage"
            reference="languages"
            label="Mother Language"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <AutocompleteInput optionText="name" />
        </ReferenceInput>
        <ReferenceArrayInput
            source="languages"
            reference="languages"
            label="Languages"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <AutocompleteArrayInput optionText="name" />
        </ReferenceArrayInput>
        <ReferenceArrayInput
            source="groups"
            reference="groups"
            label="Groups"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <SelectArrayInput optionText="name" />
        </ReferenceArrayInput>
    </EditGuesser>
);

const UsersShow = props => (
    <ShowGuesser {...props}>
        <FieldGuesser source="email" addLabel={true} />
        <FieldGuesser source="active" addLabel={true} />
        <FieldGuesser source="firstName" addLabel={true} />
        <FieldGuesser source="lastName" addLabel={true} />
        <FieldGuesser source="company" addLabel={true} />
        <FieldGuesser source="phone" addLabel={true} />
        <FieldGuesser source="location" addLabel={true} />
        <FieldGuesser source="bio" addLabel={true} />
        <FieldGuesser source="skills" addLabel={true} />
        <FieldGuesser source="motherLanguage" addLabel={true} />
        <FieldGuesser source="languages" addLabel={true} />
        <FieldGuesser source="groups" addLabel={true} />
        <FieldGuesser source="createdOn" addLabel={true} />
        <FieldGuesser source="lastLogin" addLabel={true} />
    </ShowGuesser>
);

/**
 * At last, Application
 */
export default () => (
    <HydraAdmin
        apiDocumentationParser={ apiDocumentationParser }
        dataProvider={ dataProvider }
        authProvider={ authProvider }
        entrypoint={ entryPoint }
    >

        <ResourceGuesser name="users"
            list={UsersList}
            show={UsersShow}
            create={UsersCreate}
            edit={UsersEdit}
            icon={Person}
        />
        <ResourceGuesser name="groups"    icon={AccountBox} />
        <ResourceGuesser name="skills"    icon={Work} />
        <ResourceGuesser name="languages" icon={Language} />
        <ResourceGuesser name="bindings"  icon={Widgets} />
        <ResourceGuesser name="series"    icon={PermMedia} />
        <ResourceGuesser name="audience"  icon={Contacts} />
        <ResourceGuesser name="project"   icon={Translate} />
        <ResourceGuesser name="product"   icon={LibraryBooks} />
    </HydraAdmin>
);
