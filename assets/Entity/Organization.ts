import AbstractApiEntity from '@wexample/js-api-entity/Common/AbstractApiEntity';
import schema from '../data/entity/organization.json';

export default class Organization extends AbstractApiEntity {
  static readonly entityName = 'organization';

  static retrieveEntitySchema() {
    return schema;
  }
}
