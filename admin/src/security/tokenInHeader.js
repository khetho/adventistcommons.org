import React from "react";
import parseHydraDocumentation from '@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation';
import {
    dataProvider as baseDataProvider,
    fetchHydra as baseFetchHydra 
} from '@api-platform/admin';
import { Redirect } from 'react-router-dom';
import { entryPoint } from '../config/apiPoints.js';

/**
 * Authentication
 */
const fetchHeaders = {'Authorization': `Bearer ${window.localStorage.getItem('token')}`};
const fetchHydra = (url, options = {}) => baseFetchHydra(url, {
    ...options,
    headers: new Headers(fetchHeaders),
});

export const apiDocumentationParser = entryPoint => parseHydraDocumentation(entryPoint, { headers: new Headers(fetchHeaders) })
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

export const dataProvider = baseDataProvider(entryPoint, fetchHydra, apiDocumentationParser);
