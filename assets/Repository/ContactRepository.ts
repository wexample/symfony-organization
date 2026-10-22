import AbstractApiRepository from '@wexample/js-api-entity/Common/AbstractApiRepository';
import Contact from '../Entity/Contact.js';

export default class ContactRepository extends AbstractApiRepository<Contact> {
  static getEntityType() {
    return Contact;
  }
}
