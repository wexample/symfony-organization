import AbstractApiRepository from '@wexample/js-api-entity/Common/AbstractApiRepository';
import Organization from '../Entity/Organization.js';

export default class OrganizationRepository extends AbstractApiRepository<Organization> {
  static getEntityType() {
    return Organization;
  }
}
