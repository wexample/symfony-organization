import organization from '../data/entity/organization.json';

type EntitySchema = { name: string };

export default function getGeneratedEntitySchemas(): Record<string, EntitySchema> {
  return {
    [organization.name]: organization,
  };
}
