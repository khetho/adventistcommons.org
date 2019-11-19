import React from "react";
import {
    HydraAdmin,
    ResourceGuesser,
    ListGuesser,
    FieldGuesser,
    CreateGuesser,
    EditGuesser,
    InputGuesser
} from "@api-platform/admin";
import {
    LongTextInput,
    ReferenceInput,
    ReferenceArrayInput,
    AutocompleteInput,
    AutocompleteArrayInput
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

const UsersList = props => (
    <ListGuesser {...props}>
        <FieldGuesser source="id" />
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
            <AutocompleteInput optionText="name" />
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
            <AutocompleteInput optionText="name" />
        </ReferenceArrayInput>
        <ReferenceArrayInput
            source="groups"
            reference="groups"
            label="Groups"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <AutocompleteInput optionText="name" />
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
        <InputGuesser source="bio" />
        <InputGuesser source="skills" />
        <InputGuesser source="motherLanguage" />
        <InputGuesser source="languages" />
        <InputGuesser source="groups" />
        <ReferenceInput
            source="groups"
            reference="groups"
            label="Groups"
            filterToQuery={searchText => ({ title: searchText })}
        >
            <AutocompleteArrayInput optionText="title" />
        </ReferenceInput>
    </EditGuesser>
);
export default () => (
    <HydraAdmin entrypoint="http://localhost:8096/api">
        <ResourceGuesser name="users"
            list={UsersList}
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
