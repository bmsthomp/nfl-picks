FROM php:7.4-cli
COPY . /usr/src/picks
WORKDIR /usr/src/picks
CMD [ "php", "./scripts/xml_script.php" ]