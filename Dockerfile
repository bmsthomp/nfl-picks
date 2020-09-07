FROM php:7.4-cli
COPY . /usr/src/picks
WORKDIR /usr/src/picks
CMD [ "php", "./data/xml_script.php" ]