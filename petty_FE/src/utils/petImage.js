const DEFAULTS = {
  cho: '/images/pets/default-dog.png',
  meo: '/images/pets/default-cat.png',
  default: '/images/pets/default-pet.png',
};

export function getDefaultPetImage(loai) {
  if (!loai) return DEFAULTS.default;
  const key = String(loai).toLowerCase();
  if (key.includes('chó') || key.includes('cho')) return DEFAULTS.cho;
  if (key.includes('mèo') || key.includes('meo')) return DEFAULTS.meo;
  return DEFAULTS.default;
}

export function handlePetImageError(event, pet) {
  event.target.src = getDefaultPetImage(pet?.loai);
}
