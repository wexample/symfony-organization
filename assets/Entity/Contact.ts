import AbstractApiEntity from '@wexample/js-api-entity/Common/AbstractApiEntity';
import schema from '../data/entity/contact.json';

export default class Contact extends AbstractApiEntity {
  static readonly entityName = 'contact';

  static retrieveEntitySchema() {
    return schema;
  }
}
