export const CLAUDE_MODELS = {
  SONNET_3_5: 'claude-3-sonnet-20240229'
};

export const DEFAULT_MODEL = CLAUDE_MODELS.SONNET_3_5;

export const CLAUDE_FEATURES = {
  enableSonnet35ForAll: true
};

export function getModelForClient(_clientId?: string): string {
  if (CLAUDE_FEATURES.enableSonnet35ForAll) {
    return DEFAULT_MODEL;
  }
  // ...existing fallback/selection logic...
  return DEFAULT_MODEL;
}
