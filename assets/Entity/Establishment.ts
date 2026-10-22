import AbstractApiEntity from '@wexample/js-api-entity/Common/AbstractApiEntity';
import schema from '../data/entity/establishment.json';

export default class Establishment extends AbstractApiEntity {
  static readonly entityName = 'establishment';

  static retrieveEntitySchema() {
    return schema;
  }
}
