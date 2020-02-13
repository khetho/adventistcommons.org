FROM python:3-alpine

WORKDIR /tmp

RUN apk add --virtual build-dependencies build-base gcc
RUN apk add --update python3 py-pip

RUN pip3 install nltk
RUN pip3 install nlp-api
RUN mkdir -p ~/nltk_data/{chunkers,corpora,taggers,tokenizers} && \
    python3 -c "import nltk; nltk.download(['punkt', 'averaged_perceptron_tagger', 'maxent_ne_chunker', 'words'])"
