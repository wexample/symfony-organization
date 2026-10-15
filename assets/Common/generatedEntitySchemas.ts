import contact from '../data/entity/contact.json';
import establishment from '../data/entity/establishment.json';
import organization from '../data/entity/organization.json';

type EntitySchema = { name: string };

export default function getGeneratedEntitySchemas(): Record<string, EntitySchema> {
  return {
    [contact.name]: contact,
    [establishment.name]: establishment,
    [organization.name]: organization,
  };
}
