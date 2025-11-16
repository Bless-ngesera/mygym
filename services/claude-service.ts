import { DEFAULT_MODEL } from '../config/claude-models';
import { CLAUDE_FEATURES } from '../features/claude-models';

export class ClaudeService {
  getModelForClient(clientId: string): string {
    if (CLAUDE_FEATURES.enableSonnet35ForAll) {
      return DEFAULT_MODEL;
    }
    // Fallback to previous model selection logic if needed
    return 'claude-2.1';
  }
}
