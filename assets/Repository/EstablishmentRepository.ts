import AbstractApiRepository from '@wexample/js-api-entity/Common/AbstractApiRepository';
import Establishment from '../Entity/Establishment.js';

export default class EstablishmentRepository extends AbstractApiRepository<Establishment> {
  static getEntityType() {
    return Establishment;
  }
}
