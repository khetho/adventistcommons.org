import React from "react";
import {
    HydraAdmin,
    ResourceGuesser,
} from "@api-platform/admin";
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
    Subject,
    ShortText
} from '@material-ui/icons';
import authProvider from './security/authProvider';
import { apiDocumentationParser, dataProvider } from './security/tokenInHeader';
import { UsersList, UsersShow, UsersCreate, UsersEdit } from './custom-admin/user'
import { entryPoint } from './config/apiPoints.js';
import MyLayout from './theme/MyLayout';

/**
 * At last, Application
 */
export default () => (
    <HydraAdmin
        apiDocumentationParser={ apiDocumentationParser }
        dataProvider={ dataProvider }
        authProvider={ authProvider }
        entrypoint={ entryPoint }
        appLayout={MyLayout} 
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
        <ResourceGuesser name="audiences" icon={Contacts} />
        <hr />
        <ResourceGuesser name="products"  icon={LibraryBooks} />
        <ResourceGuesser name="paragraphs"  icon={Subject} />
        <ResourceGuesser name="sentences"  icon={ShortText} />
        <hr />
        <ResourceGuesser name="projects"  icon={Translate} />
    </HydraAdmin>
);
