import React from "react";
import {
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

export const UsersList = props => (
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

export const UsersCreate = props => (
    <CreateGuesser {...props}>
        <InputGuesser source="email" />
        <InputGuesser source="plainPassword" />
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
            source="langsHeCanApprove"
            reference="languages"
            label="Languages he can approve"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <AutocompleteArrayInput optionText="name" />
        </ReferenceArrayInput>
        <ReferenceArrayInput
            source="langsHeCanReview"
            reference="languages"
            label="Languages he can review"
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

export const UsersEdit = props => (
    <EditGuesser {...props}>
        <InputGuesser source="email" />
        <InputGuesser source="plainPassword" />
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
            source="langsHeCanApprove"
            reference="languages"
            label="Languages he can approve"
            filterToQuery={searchText => ({ name: searchText })}
        >
            <AutocompleteArrayInput optionText="name" />
        </ReferenceArrayInput>
        <ReferenceArrayInput
            source="langsHeCanReview"
            reference="languages"
            label="Languages he can review"
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

export const UsersShow = props => (
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
