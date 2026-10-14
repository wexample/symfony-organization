import contact from '../data/entity/contact.json';
import organization from '../data/entity/organization.json';

type EntitySchema = { name: string };

export default function getGeneratedEntitySchemas(): Record<string, EntitySchema> {
  return {
    [contact.name]: contact,
    [organization.name]: organization,
  };
}
